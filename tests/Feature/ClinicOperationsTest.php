<?php

namespace Tests\Feature;

use App\Jobs\SendMailDelivery;
use App\Mail\AppointmentCancelled;
use App\Mail\AppointmentRescheduled;
use App\Models\Appointment;
use App\Models\AvailabilityWindow;
use App\Models\ContactMessage;
use App\Models\MailDelivery;
use App\Models\User;
use App\Services\AppointmentManagementService;
use App\Services\MailDeliveryService;
use App\Support\AppointmentLinks;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Tests\TestCase;

class ClinicOperationsTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (config('database.default') !== 'pgsql') {
            $this->markTestSkipped('Requires isolated PostgreSQL.');
        }
        if (! str_starts_with(config('database.connections.pgsql.database'), 'tasks_booking_test_')) {
            throw new RuntimeException('Use a dedicated tasks_booking_test_* database.');
        }
        config(['queue.default' => 'database', 'mail.default' => 'array', 'contact.mail_to' => 'clinic@example.test']);
    }

    /** @return array<string, string> */
    private function contactData(): array
    {
        return ['name' => 'Test Patient', 'email' => 'patient@example.test', 'phone' => '05321234567', 'phone_country' => 'TR', 'subject' => 'general', 'message' => 'A clinic question.'];
    }

    public function test_removed_task_routes_are_not_accessible(): void
    {
        foreach (['/task', '/tasks-ui', '/api/tasks', '/api/tasks/1'] as $path) {
            $this->getJson($path)->assertNotFound();
            $this->postJson($path, ['title' => 'Unwanted task'])->assertNotFound();
            $this->patchJson($path, ['title' => 'Unwanted change'])->assertNotFound();
            $this->deleteJson($path)->assertNotFound();
        }
    }

    public function test_contact_submission_is_queued_and_rate_limit_is_shared_between_languages(): void
    {
        Queue::fake([SendMailDelivery::class]);
        Mail::fake();

        $this->postJson('/tr/contact', $this->contactData())->assertCreated();
        $this->postJson('/contact', $this->contactData())->assertCreated();
        $this->postJson('/tr/contact', $this->contactData())->assertCreated();
        $this->postJson('/contact', $this->contactData())->assertTooManyRequests()->assertJsonPath('message', __('contact.form.rate_limited', [], 'en'));

        $this->assertDatabaseCount('contact_messages', 3);
        $this->assertDatabaseHas('mail_deliveries', ['kind' => 'contact', 'recipient' => 'clinic@example.test', 'status' => 'pending', 'locale' => 'tr']);
        Queue::assertPushed(SendMailDelivery::class, 3);
        Mail::assertNothingSent();
    }

    public function test_invalid_contact_does_not_queue_mail(): void
    {
        Queue::fake([SendMailDelivery::class]);

        $this->postJson('/contact', [])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'phone', 'subject', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
        Queue::assertNothingPushed();
    }

    public function test_contact_admin_pages_and_retry_require_admin_and_escape_message(): void
    {
        $message = ContactMessage::create([...$this->contactData(), 'locale' => 'tr', 'message' => '<script>alert(1)</script>']);
        $delivery = app(MailDeliveryService::class)->contact($message);
        $this->get('/admin/contacts')->assertRedirect();
        $this->actingAs(User::factory()->create(['is_admin' => false]));
        $this->get('/admin/contacts/'.$message->id)->assertForbidden();
        $this->post('/admin/contacts/'.$message->id.'/resend')->assertForbidden();
        $this->post('/admin/deliveries/'.$delivery->id.'/retry')->assertForbidden();
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->get('/tr/admin/contacts')->assertSee('Test Patient');
        $this->get('/tr/admin/contacts/'.$message->id)->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->post('/admin/contacts/'.$message->id.'/resend')->assertRedirect();

        $this->assertDatabaseCount('mail_deliveries', 1);
    }

    public function test_patient_cancel_queues_patient_and_clinic_notifications_once(): void
    {
        $this->freezeTime();
        Queue::fake([SendMailDelivery::class]);
        $appointment = Appointment::factory()->create();
        $url = AppointmentLinks::for($appointment, 'cancel');

        $this->post($url, ['confirm_cancel' => 1])->assertRedirect();
        $this->post($url, ['confirm_cancel' => 1])->assertRedirect();

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('mail_deliveries', ['kind' => 'cancelled', 'recipient' => $appointment->email]);
        $this->assertDatabaseHas('mail_deliveries', ['kind' => 'clinic_cancelled', 'recipient' => 'clinic@example.test']);
        Queue::assertPushed(SendMailDelivery::class, 2);
        foreach (MailDelivery::all() as $delivery) {
            (new SendMailDelivery($delivery->id))->handle();
        }
        $this->assertSame(2, MailDelivery::where('status', 'sent')->count());
        app()->setLocale('tr');
        (new AppointmentCancelled($appointment->load('treatment'), true))->assertSeeInHtml('Hasta randevusunu iptal etti');
    }

    public function test_rescheduled_email_shows_old_and_new_times_in_both_languages(): void
    {
        $this->freezeTime();
        $appointment = Appointment::factory()->create(['starts_at' => '2026-10-15 07:00:00', 'ends_at' => '2026-10-15 07:30:00']);
        foreach (['tr' => 'Randevunuz Güncellendi', 'en' => 'Your Appointment Has Been Updated'] as $locale => $title) {
            app()->setLocale($locale);
            $mail = new AppointmentRescheduled($appointment->load('treatment'), '2026-10-15T06:00:00Z');
            $mail->assertSeeInHtml($title);
            $mail->assertSeeInHtml('09:00');
            $mail->assertSeeInHtml('10:00');
            $mail->assertSeeInText('09:00');
            $mail->assertSeeInText('10:00');
        }
    }

    public function test_obsolete_confirmation_is_skipped_after_cancellation(): void
    {
        $this->freezeTime();
        $appointment = Appointment::factory()->create();
        $delivery = app(MailDeliveryService::class)->appointment($appointment, 'confirmed');
        app(AppointmentManagementService::class)->cancel($appointment);
        Mail::fake();

        (new SendMailDelivery($delivery->id))->handle();

        $this->assertDatabaseHas('mail_deliveries', ['id' => $delivery->id, 'status' => 'skipped']);
        Mail::assertNothingSent();
    }

    public function test_worker_sends_contact_once_and_tracks_success(): void
    {
        $message = ContactMessage::create([...$this->contactData(), 'locale' => 'en']);
        $delivery = app(MailDeliveryService::class)->contact($message);
        $job = new SendMailDelivery($delivery->id);

        $job->handle();
        $job->handle();

        $this->assertDatabaseHas('mail_deliveries', ['id' => $delivery->id, 'status' => 'sent', 'attempts' => 1]);
        $this->assertNotNull($delivery->fresh()->sent_at);
    }

    public function test_failed_worker_attempt_can_retry_and_exhausted_delivery_is_visible_to_admin(): void
    {
        $message = ContactMessage::create([...$this->contactData(), 'locale' => 'en']);
        $delivery = app(MailDeliveryService::class)->contact($message);
        $job = new SendMailDelivery($delivery->id);
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('locale')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new RuntimeException('Transport unavailable'));
        try {
            $job->handle();
            $this->fail('A transport failure must reach the queue worker.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Transport unavailable', $exception->getMessage());
        }
        $this->assertDatabaseHas('mail_deliveries', ['id' => $delivery->id, 'status' => 'pending', 'attempts' => 1]);
        $job->failed(new RuntimeException('Attempts exhausted'));
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->get('/tr/admin')->assertSee('Başarısız e-postalar')->assertSee('clinic@example.test');

        $this->post('/admin/deliveries/'.$delivery->id.'/retry')->assertRedirect();
        $this->post('/admin/deliveries/'.$delivery->id.'/retry')->assertSessionHasErrors('delivery');
        Mail::swap(new MailManager($this->app));
        $job->handle();

        $this->assertDatabaseHas('mail_deliveries', ['id' => $delivery->id, 'status' => 'sent', 'attempts' => 2]);
    }

    public function test_dashboard_uses_clinic_day_and_next_confirmed_appointment(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-02T06:00:00Z'));
        Appointment::factory()->create(['name' => 'Today Patient', 'starts_at' => '2026-10-02 07:00:00', 'ends_at' => '2026-10-02 07:30:00']);
        Appointment::factory()->create(['name' => 'Tomorrow Patient', 'starts_at' => '2026-10-02 22:00:00', 'ends_at' => '2026-10-02 22:30:00']);
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->get('/tr/admin')->assertSee('Today Patient')->assertDontSee('Tomorrow Patient')->assertViewHas('todayCount', 1);
    }

    public function test_database_worker_retries_three_times_then_marks_delivery_failed(): void
    {
        $this->freezeTime();
        $message = ContactMessage::create([...$this->contactData(), 'locale' => 'en']);
        $delivery = app(MailDeliveryService::class)->contact($message);
        Mail::shouldReceive('to')->times(3)->andReturnSelf();
        Mail::shouldReceive('locale')->times(3)->andReturnSelf();
        Mail::shouldReceive('send')->times(3)->andThrow(new RuntimeException('Test transport unavailable'));

        foreach ([0, 61, 301] as $seconds) {
            $this->travel($seconds)->seconds();
            Artisan::call('queue:work', ['connection' => 'database', '--once' => true, '--sleep' => 0, '--no-interaction' => true]);
        }

        $this->assertDatabaseHas('mail_deliveries', ['id' => $delivery->id, 'status' => 'failed', 'attempts' => 3]);
        $this->assertDatabaseCount('jobs', 0);
        $this->assertDatabaseCount('failed_jobs', 1);
    }

    public function test_second_reschedule_before_worker_runs_sends_only_the_latest_change(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-01T06:00:00Z'));
        $appointment = Appointment::factory()->create(['starts_at' => '2026-10-15 06:00:00', 'ends_at' => '2026-10-15 06:30:00']);
        AvailabilityWindow::factory()->create(['starts_at' => '2026-10-15 06:00:00', 'ends_at' => '2026-10-15 12:00:00']);
        $management = app(AppointmentManagementService::class);

        $management->reschedule($appointment, CarbonImmutable::parse('2026-10-15T07:00:00Z'));
        $management->reschedule($appointment, CarbonImmutable::parse('2026-10-15T08:00:00Z'));
        foreach (MailDelivery::orderBy('id')->get() as $delivery) {
            (new SendMailDelivery($delivery->id))->handle();
        }

        $this->assertSame(1, MailDelivery::where('status', 'skipped')->count());
        $this->assertSame(1, MailDelivery::where('status', 'sent')->count());
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'starts_at' => '2026-10-15 08:00:00', 'mail_status' => 'sent']);
    }
}
