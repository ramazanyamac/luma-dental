<?php

namespace App\Support;

use App\Models\Appointment;
use Illuminate\Support\Facades\URL;

class AppointmentLinks
{
    public static function for(Appointment $appointment, string $action = 'show'): string
    {
        return URL::temporarySignedRoute(
            'appointment.manage.'.$action,
            $appointment->starts_at->max(now())->addDays(30),
            ['appointment' => $appointment->reference]
        );
    }
}
