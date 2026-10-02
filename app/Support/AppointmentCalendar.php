<?php

namespace App\Support;

use App\Models\Appointment;

class AppointmentCalendar
{
    public static function render(Appointment $appointment): string
    {
        $escape = fn (string $value): string => str_replace(['\\', "\r\n", "\n", ',', ';'], ['\\\\', '\\n', '\\n', '\\,', '\\;'], $value);
        $lines = [
            'BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//Luma Dental//Appointments//EN',
            'CALSCALE:GREGORIAN', 'BEGIN:VEVENT',
            'UID:'.$appointment->reference.'@luma-dental',
            'DTSTAMP:'.now()->utc()->format('Ymd\THis\Z'),
            'DTSTART:'.$appointment->starts_at->utc()->format('Ymd\THis\Z'),
            'DTEND:'.$appointment->ends_at->utc()->format('Ymd\THis\Z'),
            'SUMMARY:'.$escape('Luma Dental — '.__('appointment.treatments.'.$appointment->treatment->code)),
            'LOCATION:'.$escape(__('appointment.review.address')),
            'STATUS:'.($appointment->status === 'cancelled' ? 'CANCELLED' : 'CONFIRMED'),
            'END:VEVENT', 'END:VCALENDAR',
        ];

        return collect($lines)->map(function (string $line): string {
            $folded = '';
            $length = 0;
            foreach (mb_str_split($line) as $character) {
                if ($length + strlen($character) > 75) {
                    $folded .= "\r\n ";
                    $length = 1;
                }
                $folded .= $character;
                $length += strlen($character);
            }

            return $folded;
        })->implode("\r\n")."\r\n";
    }
}
