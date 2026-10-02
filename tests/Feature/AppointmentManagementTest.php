<?php

namespace Tests\Feature;

use App\Jobs\SendMailDelivery;
use App\Mail\AppointmentCancelled;
use App\Mail\AppointmentConfirmed;
use App\Models\Appointment;
use App\Models\AvailabilityWindow;
use App\Models\MailDelivery;
use App\Models\Treatment;
use App\Models\User;
use App\Support\AppointmentLinks;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AppointmentManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['queue.default' => 'database']);
        if (config('database.default') !== 'pgsql') {
            $this->markTestSkipped('Requires an isolated PostgreSQL database.');
        }
        if (! str_starts_with(config('database.connections.pgsql.database'), 'tasks_booking_test_')) {
            throw new \RuntimeException('Use a dedicated tasks_booking_test_* database.');
        }
    }

    private function appointment(): Appointment
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-01T06:00:00Z'));
        $treatment = Treatment::factory()->create(['code' => 'general_checkup']);
        AvailabilityWindow::factory()->create(['starts_at' => '2026-10-15 06:00:00', 'ends_at' => '2026-10-15 12:00:00']);

        return Appointment::factory()->create([
            'treatment_id' => $treatment->id, 'name' => 'Test Patient', 'email' => 'patient@example.test',
            'starts_at' => '2026-10-15 06:00:00', 'ends_at' => '2026-10-15 06:30:00',
        ]);
    }

    public function test_guests_and_non_admins_cannot_access_or_change_appointments(): void
    {
        $appointment = $this->appointment();
        $this->get('/admin/appointments')->assertRedirect();
        $this->actingAs(User::factory()->create(['is_admin' => false]));
        $this->get('/admin/appointments')->assertForbidden();
        $this->get('/admin/appointments/'.$appointment->id)->assertForbidden();
        $this->post('/admin/appointments/'.$appointment->id.'/cancel', ['confirm_cancel' => 1])->assertForbidden();
        $this->post('/admin/appointments/'.$appointment->id.'/resend')->assertForbidden();
        $this->patch('/admin/appointments/'.$appointment->id, ['starts_at' => '2026-10-15T10:00'])->assertForbidden();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'confirmed', 'starts_at' => '2026-10-15 06:00:00']);
    }

    public function test_admin_can_filter_list_and_view_localized_details(): void
    {
        $appointment = $this->appointment();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->get('/tr/admin/appointments?search=Test&date=2026-10-15&status=confirmed')->assertOk()->assertSee('Test Patient');
        $this->get('/tr/admin/appointments?date=2026-10-16')->assertOk()->assertDontSee('Test Patient');
        $this->get('/tr/admin/appointments/'.$appointment->id)->assertOk()->assertSee('Onay e-postası')->assertSee('Tarih ve saati değiştir');
    }

    public function test_admin_reschedule_changes_time_and_sends_confirmation(): void
    {
        $appointment = $this->appointment();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        config(['mail.default' => 'array']);
        $this->patch('/tr/admin/appointments/'.$appointment->id, ['starts_at' => '2026-10-15T10:00'])->assertRedirect();
        $this->deliverQueuedMail();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'starts_at' => '2026-10-15 07:00:00', 'ends_at' => '2026-10-15 07:30:00', 'mail_status' => 'sent']);
        $this->assertNotNull($appointment->fresh()->confirmation_sent_at);
    }

    public function test_reschedule_conflict_preserves_original_time_and_sends_no_mail(): void
    {
        $appointment = $this->appointment();
        Appointment::factory()->create(['treatment_id' => $appointment->treatment_id, 'starts_at' => '2026-10-15 07:00:00', 'ends_at' => '2026-10-15 07:30:00']);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        Mail::fake();
        $this->patch('/admin/appointments/'.$appointment->id, ['starts_at' => '2026-10-15T10:00'])->assertSessionHasErrors('starts_at');
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'starts_at' => '2026-10-15 06:00:00']);
        Mail::assertNothingSent();
    }

    public function test_admin_resend_persists_delivery_status_without_creating_another_booking(): void
    {
        $appointment = $this->appointment();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        config(['mail.default' => 'array']);
        $this->post('/admin/appointments/'.$appointment->id.'/resend')->assertRedirect();
        $this->deliverQueuedMail();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'mail_status' => 'sent']);
        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_failed_resend_is_persisted_and_can_be_retried(): void
    {
        $appointment = $this->appointment();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('locale')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('Test mail failure'));
        $this->post('/admin/appointments/'.$appointment->id.'/resend')->assertRedirect();
        $this->deliverQueuedMail();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'confirmed', 'mail_status' => 'failed']);
        $this->deliverQueuedMail();
        $this->assertNotNull($appointment->fresh()->mail_attempted_at);
    }

    public function test_signed_details_link_works_but_unsigned_tampered_and_expired_links_fail(): void
    {
        $appointment = $this->appointment();
        $url = AppointmentLinks::for($appointment);
        $this->get($url)->assertOk()->assertSee('Test Patient')->assertHeader('Referrer-Policy', 'no-referrer');
        $this->get(route('appointment.manage.show', $appointment->reference))->assertForbidden();
        $this->get($url.'&changed=1')->assertForbidden();
        $expired = URL::temporarySignedRoute('appointment.manage.show', now()->subMinute(), ['appointment' => $appointment->reference]);
        $this->get($expired)->assertForbidden();
    }

    public function test_patient_cancellation_requires_confirmation_and_releases_the_slot(): void
    {
        $appointment = $this->appointment();
        $url = AppointmentLinks::for($appointment, 'cancel');
        $this->post($url)->assertSessionHasErrors('confirm_cancel');
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'confirmed']);
        $this->post($url, ['confirm_cancel' => 1])->assertRedirect();
        $this->post($url, ['confirm_cancel' => 1])->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'cancelled']);
        $slots = $this->getJson('/appointment/slots?date=2026-10-15&treatment_id='.$appointment->treatment_id)->assertOk()->json('slots');
        $this->assertSame('2026-10-15T06:00:00+00:00', $slots[0]['starts_at']);
        $this->get(AppointmentLinks::for($appointment))->assertOk()->assertSee('İptal edildi')->assertDontSee('name="confirm_cancel"', false);
    }

    public function test_patient_cannot_cancel_with_only_a_reference_or_show_signature(): void
    {
        $appointment = $this->appointment();
        $this->post(route('appointment.manage.cancel', $appointment->reference), ['confirm_cancel' => 1])->assertForbidden();
        $showUrl = AppointmentLinks::for($appointment);
        $this->post(str_replace('/'.$appointment->reference.'?', '/'.$appointment->reference.'/cancel?', $showUrl), ['confirm_cancel' => 1])->assertForbidden();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'confirmed']);
    }

    public function test_past_appointments_cannot_be_cancelled(): void
    {
        $appointment = $this->appointment();
        $this->travelTo(CarbonImmutable::parse('2026-10-16T06:00:00Z'));
        $this->post(AppointmentLinks::for($appointment, 'cancel'), ['confirm_cancel' => 1])->assertSessionHasErrors('appointment');
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'confirmed']);
    }

    public function test_cancelled_appointments_cannot_be_rescheduled_or_sent_as_confirmed(): void
    {
        $appointment = $this->appointment();
        $appointment->update(['status' => 'cancelled']);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        Mail::fake();
        $this->post('/admin/appointments/'.$appointment->id.'/resend')->assertRedirect();
        $this->patch('/admin/appointments/'.$appointment->id, ['starts_at' => '2026-10-15T10:00'])->assertSessionHasErrors('appointment');
        Mail::assertNotSent(AppointmentConfirmed::class);
        $this->deliverQueuedMail();
        Mail::assertSent(AppointmentCancelled::class);
    }

    public function test_signed_calendar_contains_utc_times_and_download_headers(): void
    {
        $appointment = $this->appointment();
        $this->get(AppointmentLinks::for($appointment, 'calendar'))->assertOk()
            ->assertHeader('Content-Type', 'text/calendar; charset=UTF-8')
            ->assertSee('DTSTART:20261015T060000Z')->assertSee('DTEND:20261015T063000Z')
            ->assertSee('UID:'.$appointment->reference.'@luma-dental');
    }

    public function test_confirmation_mail_contains_private_management_link(): void
    {
        $appointment = $this->appointment();
        app()->setLocale('tr');
        $mail = new AppointmentConfirmed($appointment->load('treatment'));
        $mail->assertSeeInHtml('Randevumu Görüntüle / Yönet');
        $mail->assertSeeInText(AppointmentLinks::for($appointment));
    }

    public function test_admin_cancellation_notifies_the_patient_once_in_their_language(): void
    {
        $appointment = $this->appointment();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        Mail::fake();

        $this->post('/admin/appointments/'.$appointment->id.'/cancel', ['confirm_cancel' => 1])->assertRedirect();
        $this->post('/admin/appointments/'.$appointment->id.'/cancel', ['confirm_cancel' => 1])->assertRedirect();

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'cancelled']);
        $this->deliverQueuedMail();
        Mail::assertSent(AppointmentCancelled::class, 1);
        $this->deliverQueuedMail();
        Mail::assertSent(AppointmentCancelled::class, fn ($mail) => $mail->hasTo('patient@example.test') && $mail->locale === 'tr');
    }

    public function test_admin_cancellation_persists_delivery_status_and_renders_cancellation_content(): void
    {
        $appointment = $this->appointment();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        config(['mail.default' => 'array']);

        $this->post('/tr/admin/appointments/'.$appointment->id.'/cancel', ['confirm_cancel' => 1])->assertRedirect()->assertSessionHas('mail_status', 'E-posta gönderim kuyruğuna alındı.');

        $this->deliverQueuedMail();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'cancelled', 'cancellation_mail_status' => 'sent']);
        $this->assertNotNull($appointment->fresh()->cancellation_sent_at);
        $this->get('/tr/admin/appointments/'.$appointment->id)->assertSee('İptal e-postası');
        foreach (['tr' => 'Randevunuz İptal Edildi', 'en' => 'Your Appointment Has Been Cancelled'] as $locale => $title) {
            app()->setLocale($locale);
            $mail = new AppointmentCancelled($appointment->fresh()->load('treatment'));
            $mail->assertSeeInHtml($title);
            $mail->assertSeeInText(__('appointment.cancellation.description'));
            $mail->assertDontSeeInHtml(__('appointment.success.title'));
        }
    }

    public function test_failed_cancellation_mail_preserves_cancellation_and_admin_can_retry(): void
    {
        $appointment = $this->appointment();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('locale')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('Test mail failure'));

        $this->post('/admin/appointments/'.$appointment->id.'/cancel', ['confirm_cancel' => 1])->assertRedirect();

        $this->deliverQueuedMail();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'cancelled', 'cancellation_mail_status' => 'failed']);
        $this->assertNotNull($appointment->fresh()->cancellation_mail_attempted_at);
        Mail::swap(new MailManager($this->app));
        config(['mail.default' => 'array']);
        $this->post('/admin/appointments/'.$appointment->id.'/resend')->assertRedirect();
        $this->deliverQueuedMail();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'cancelled', 'cancellation_mail_status' => 'sent']);
    }

    private function deliverQueuedMail(): void
    {
        foreach (MailDelivery::where('status', 'pending')->get() as $delivery) {
            $job = new SendMailDelivery($delivery->id);
            try {
                $job->handle();
            } catch (\RuntimeException $exception) {
                $job->failed($exception);
            }
        }
    }
}
