<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AvailabilityWindow;
use App\Models\Treatment;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Propaganistas\LaravelPhone\PhoneNumber;

class AppointmentBookingService
{
    public function __construct(private MailDeliveryService $deliveries) {}

    public function book(array $data): Appointment
    {
        try {
            return DB::transaction(function () use ($data): Appointment {
                $existing = Appointment::where('reference', $data['booking_token'])->first();
                if ($existing) {
                    return $existing;
                }

                $treatment = Treatment::whereKey($data['treatment_id'])->where('is_active', true)->sharedLock()->first();
                if (! $treatment || $treatment->duration_minutes <= 0) {
                    $this->unavailable();
                }

                $start = CarbonImmutable::parse($data['starts_at'])->utc();
                $end = $start->addMinutes($treatment->duration_minutes);
                $window = AvailabilityWindow::where('starts_at', '<=', $start)
                    ->where('ends_at', '>=', $end)->lockForUpdate()->first();

                // A retry may have waited for the first request to commit.
                $existing = Appointment::where('reference', $data['booking_token'])->first();
                if ($existing) {
                    return $existing;
                }

                if (! $window || $start->lessThanOrEqualTo(CarbonImmutable::now('UTC'))
                    || ($start->getTimestamp() - $window->starts_at->getTimestamp()) % 1800 !== 0
                    || Appointment::where('status', 'confirmed')->where('starts_at', '<', $end)
                        ->where('ends_at', '>', $start)->exists()) {
                    $this->unavailable();
                }

                $appointment = new Appointment([
                    'treatment_id' => $treatment->id,
                    'starts_at' => $start,
                    'ends_at' => $end,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => (new PhoneNumber($data['phone'], $data['phone_country']))->formatE164(),
                    'birth_date' => $data['birth_date'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'reason' => $data['reason'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'is_new_patient' => $data['is_new_patient'] ?? false,
                    'locale' => app()->getLocale(),
                    'status' => 'confirmed',
                ]);
                $appointment->reference = $data['booking_token'];
                $appointment->save();
                $this->deliveries->appointment($appointment, 'confirmed');

                return $appointment;
            });
        } catch (QueryException $exception) {
            if (in_array($exception->getCode(), ['23P01', '23505'], true)) {
                $existing = Appointment::where('reference', $data['booking_token'])->first();
                if ($existing) {
                    return $existing;
                }
                $this->unavailable();
            }
            throw $exception;
        }
    }

    private function unavailable(): never
    {
        throw ValidationException::withMessages(['starts_at' => __('appointment.booking.unavailable')]);
    }
}
