<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AvailabilityWindow;
use App\Models\Treatment;
use Carbon\CarbonImmutable;

class AppointmentAvailabilityService
{
    public function slotsForDate(Treatment $treatment, string $date): array
    {
        $timezone = config('appointments.timezone');

        $dayStart = CarbonImmutable::createFromFormat(
            '!Y-m-d',
            $date,
            $timezone
        );

        return $this->slotsBetween(
            $treatment,
            $dayStart->utc(),
            $dayStart->addDay()->utc()
        );
    }

    public function datesForMonth(Treatment $treatment, string $month): array
    {
        $timezone = config('appointments.timezone');

        $monthStart = CarbonImmutable::createFromFormat(
            '!Y-m-d',
            $month . '-01',
            $timezone
        );

        $slots = $this->slotsBetween(
            $treatment,
            $monthStart->utc(),
            $monthStart->addMonth()->utc()
        );

        return collect($slots)
            ->pluck('date')
            ->unique()
            ->values()
            ->all();
    }

    private function slotsBetween(
        Treatment $treatment,
        CarbonImmutable $periodStart,
        CarbonImmutable $periodEnd
    ): array {
        if (!$treatment->is_active || $treatment->duration_minutes <= 0) {
            return [];
        }

        $timezone = config('appointments.timezone');
        $now = CarbonImmutable::now('UTC');

        $windows = AvailabilityWindow::query()
            ->where('starts_at', '>=', $periodStart)
            ->where('starts_at', '<', $periodEnd)
            ->where('ends_at', '>', $now)
            ->orderBy('starts_at')
            ->get();

        $appointments = Appointment::query()
            ->where('status', 'confirmed')
            ->where('starts_at', '<', $periodEnd)
            ->where('ends_at', '>', $periodStart)
            ->get(['starts_at', 'ends_at']);

        $slots = [];
        $intervalMinutes = 30;

        foreach ($windows as $window) {
            $cursor = $window->starts_at->utc();

            while (
                $cursor->addMinutes($treatment->duration_minutes)
                ->lessThanOrEqualTo($window->ends_at)
            ) {
                $endsAt = $cursor->addMinutes(
                    $treatment->duration_minutes
                );

                $overlaps = $appointments->contains(
                    fn(Appointment $appointment) =>
                    $appointment->starts_at->lessThan($endsAt)
                        && $appointment->ends_at->greaterThan($cursor)
                );

                if ($cursor->greaterThan($now) && !$overlaps) {
                    $localStart = $cursor->setTimezone($timezone);

                    $slots[] = [
                        'availability_window_id' => $window->id,
                        'date' => $localStart->toDateString(),
                        'starts_at' => $cursor->toIso8601String(),
                        'ends_at' => $endsAt->toIso8601String(),
                        'label' => $localStart->format('H:i'),
                    ];
                }

                $cursor = $cursor->addMinutes($intervalMinutes);
            }
        }

        return $slots;
    }
}
