<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AvailabilityWindow;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentManagementService
{
    public function __construct(private MailDeliveryService $deliveries) {}

    public function cancel(Appointment $appointment, bool $byPatient = false): bool
    {
        return DB::transaction(function () use ($appointment, $byPatient): bool {
            $current = Appointment::whereKey($appointment->id)->lockForUpdate()->firstOrFail();
            if ($current->status === 'cancelled') {
                return false;
            }
            $this->ensureEditable($current);
            $current->status = 'cancelled';
            $current->save();
            $this->deliveries->appointment($current, 'cancelled');
            if ($byPatient) {
                $this->deliveries->appointment($current, 'clinic_cancelled');
            }

            return true;
        });
    }

    public function reschedule(Appointment $appointment, CarbonImmutable $start): void
    {
        try {
            DB::transaction(function () use ($appointment, $start): void {
                $window = AvailabilityWindow::where('starts_at', '<=', $start)->where('ends_at', '>', $start)
                    ->lockForUpdate()->first();
                $current = Appointment::whereKey($appointment->id)->lockForUpdate()->firstOrFail();
                $this->ensureEditable($current);
                $duration = $current->starts_at->diffInMinutes($current->ends_at);
                $end = $start->addMinutes($duration);

                if (! $window || $start->lessThanOrEqualTo(now()) || $end->greaterThan($window->ends_at)
                    || ($start->getTimestamp() - $window->starts_at->getTimestamp()) % 1800 !== 0
                    || Appointment::where('status', 'confirmed')->whereKeyNot($current->id)
                        ->where('starts_at', '<', $end)->where('ends_at', '>', $start)->exists()) {
                    $this->unavailable();
                }

                $oldStart = $current->starts_at->toIso8601String();
                $current->starts_at = $start;
                $current->ends_at = $end;
                $current->mail_status = 'pending';
                $current->confirmation_sent_at = null;
                $current->save();
                $this->deliveries->appointment($current, 'rescheduled', ['old_starts_at' => $oldStart], resend: true);
            });
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23P01') {
                $this->unavailable();
            }
            throw $exception;
        }
    }

    private function ensureEditable(Appointment $appointment): void
    {
        if ($appointment->status !== 'confirmed' || $appointment->starts_at->lessThanOrEqualTo(now())) {
            throw ValidationException::withMessages(['appointment' => __('management.not_editable')]);
        }
    }

    private function unavailable(): never
    {
        throw ValidationException::withMessages(['starts_at' => __('appointment.booking.unavailable')]);
    }
}
