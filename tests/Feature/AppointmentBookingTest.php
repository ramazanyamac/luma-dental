<?php

namespace Tests\Feature;

use App\Jobs\SendMailDelivery;
use App\Mail\AppointmentConfirmed;
use App\Models\Appointment;
use App\Models\AvailabilityWindow;
use App\Models\MailDelivery;
use App\Models\Treatment;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['queue.default' => 'database']);
        if (config('database.default') !== 'pgsql') {
            $this->markTestSkipped('Booking tests require an isolated PostgreSQL database, including exclusion constraints.');
        }
        $database = config('database.connections.pgsql.database');
        if (! str_starts_with($database, 'tasks_booking_test_')) {
            throw new \RuntimeException('Use a dedicated tasks_booking_test_* database for these tests.');
        }
    }

    private function payload(array $changes = []): array
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-01T06:00:00Z'));
        $treatment = Treatment::factory()->create(['code' => 'general']);
        AvailabilityWindow::factory()->create(['starts_at' => '2026-10-15 06:00:00', 'ends_at' => '2026-10-15 09:00:00']);
        $token = (string) Str::uuid();
        $this->withSession(['appointment.tokens' => [$token => true]]);

        return array_replace([
            'booking_token' => $token, 'treatment_id' => $treatment->id,
            'starts_at' => '2026-10-15T06:00:00+00:00',
            'name' => 'Test Patient', 'email' => 'patient@example.test',
            'phone' => '05321234567', 'phone_country' => 'TR',
            'is_new_patient' => true,
        ], $changes);
    }

    public function test_booking_page_renders_review_and_success_steps_in_both_languages(): void
    {
        $this->payload();
        $this->get('/appointment')->assertOk()->assertSee('Review Your Appointment');
        $this->get('/tr/appointment')->assertOk()->assertSee('Randevunuzu Gözden Geçirin');
    }

    public function test_details_validation_normalizes_phone_without_creating_an_appointment(): void
    {
        $data = $this->payload();
        Mail::fake();
        $this->postJson('/tr/appointment/details/validate', $data)->assertOk()->assertJsonPath('phone', '+905321234567');
        $this->assertDatabaseCount('appointments', 0);
        Mail::assertNothingSent();
    }

    public function test_booking_persists_normalized_details_and_sends_localized_mail(): void
    {
        $data = $this->payload();
        Mail::fake();
        $this->postJson('/tr/appointment', $data)->assertCreated()->assertJsonPath('reference', $data['booking_token']);
        $this->assertDatabaseHas('appointments', ['reference' => $data['booking_token'], 'phone' => '+905321234567', 'locale' => 'tr', 'status' => 'confirmed', 'ends_at' => '2026-10-15 06:30:00']);
        $this->deliverQueuedMail();
        Mail::assertSent(AppointmentConfirmed::class, fn ($mail) => $mail->hasTo('patient@example.test') && $mail->locale === 'tr');
    }

    public function test_retry_returns_existing_booking_without_duplicate_or_second_email(): void
    {
        $data = $this->payload();
        Mail::fake();
        $this->postJson('/appointment', $data)->assertCreated();
        $this->postJson('/appointment', $data)->assertOk()->assertJsonPath('reference', $data['booking_token']);
        $this->assertDatabaseCount('appointments', 1);
        $this->deliverQueuedMail();
        Mail::assertSent(AppointmentConfirmed::class, 1);
    }

    public function test_another_patient_cannot_book_an_overlapping_time(): void
    {
        $data = $this->payload();
        Mail::fake();
        $this->postJson('/appointment', $data)->assertCreated();
        $token = (string) Str::uuid();
        $this->withSession(['appointment.tokens' => [$token => true]])
            ->postJson('/appointment', [...$data, 'booking_token' => $token])
            ->assertUnprocessable()->assertJsonValidationErrors('starts_at');
        $this->assertDatabaseCount('appointments', 1);
        $this->deliverQueuedMail();
        Mail::assertSent(AppointmentConfirmed::class, 1);
    }

    #[DataProvider('unavailableTimes')]
    public function test_invalid_slot_is_rejected_without_booking_or_mail(string $start): void
    {
        $data = $this->payload(['starts_at' => $start]);
        Mail::fake();
        $this->postJson('/appointment', $data)->assertUnprocessable()
            ->assertJsonPath('errors.starts_at.0', __('appointment.booking.unavailable', [], 'en'));
        $this->assertDatabaseCount('appointments', 0);
        Mail::assertNothingSent();
    }

    public static function unavailableTimes(): array
    {
        return [
            'past' => ['2026-09-15T06:00:00+00:00'],
            'off grid' => ['2026-10-15T06:15:00+00:00'],
            'outside window' => ['2026-10-15T09:00:00+00:00'],
        ];
    }

    public function test_invalid_personal_details_and_foreign_token_are_rejected(): void
    {
        $data = $this->payload(['email' => 'invalid', 'booking_token' => (string) Str::uuid()]);
        Mail::fake();
        $this->postJson('/appointment', $data)->assertUnprocessable()->assertJsonValidationErrors(['email', 'booking_token']);
        $this->assertDatabaseCount('appointments', 0);
        Mail::assertNothingSent();
    }

    public function test_inactive_treatment_is_rejected(): void
    {
        $data = $this->payload();
        Treatment::findOrFail($data['treatment_id'])->update(['is_active' => false]);
        Mail::fake();
        $this->postJson('/appointment', $data)->assertUnprocessable()->assertJsonValidationErrors('treatment_id');
        $this->assertDatabaseCount('appointments', 0);
        Mail::assertNothingSent();
    }

    public function test_email_failure_preserves_booking_and_reports_unsent_status(): void
    {
        $data = $this->payload();
        Mail::shouldReceive('to')->once()->with('patient@example.test')->andReturnSelf();
        Mail::shouldReceive('locale')->once()->with('en')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('Test transport unavailable'));
        $this->postJson('/appointment', $data)->assertCreated()->assertJsonPath('mail_sent', false);
        $this->deliverQueuedMail();
        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_array_transport_renders_email_and_reports_accepted_delivery(): void
    {
        $data = $this->payload();
        config(['mail.default' => 'array']);
        $this->postJson('/appointment', $data)->assertCreated()->assertJsonPath('mail_status', 'pending');
        $this->deliverQueuedMail();
        $this->assertDatabaseHas('appointments', ['reference' => $data['booking_token'], 'mail_status' => 'sent']);
        $appointment = Appointment::with('treatment')->firstOrFail();
        $mail = new AppointmentConfirmed($appointment);
        $mail->assertSeeInHtml('Test Patient');
        $mail->assertSeeInText($appointment->reference);
    }

    public function test_database_prevents_overlaps_even_outside_the_booking_service(): void
    {
        $data = $this->payload();
        Mail::fake();
        $this->postJson('/appointment', $data)->assertCreated();
        $this->deliverQueuedMail();
        Mail::assertSent(AppointmentConfirmed::class, 1);
        $duplicate = Appointment::firstOrFail()->replicate(['reference']);
        $duplicate->starts_at = '2026-10-15 06:15:00';
        $duplicate->ends_at = '2026-10-15 06:45:00';
        $this->expectException(QueryException::class);
        $duplicate->save();
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
