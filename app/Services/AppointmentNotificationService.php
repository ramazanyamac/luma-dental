<?php

namespace App\Services;

use App\Models\Appointment;

class AppointmentNotificationService
{
    public function __construct(private MailDeliveryService $deliveries) {}

    public function send(Appointment $appointment): bool
    {
        $this->deliveries->appointment($appointment, 'confirmed', resend: true);

        return $appointment->fresh()->mail_status === 'sent';
    }

    public function sendCancellation(Appointment $appointment, bool $resend = false): bool
    {
        $this->deliveries->appointment($appointment, 'cancelled', resend: $resend);

        return $appointment->fresh()->cancellation_mail_status === 'sent';
    }
}
