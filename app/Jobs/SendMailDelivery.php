<?php

namespace App\Jobs;

use App\Mail\AppointmentCancelled;
use App\Mail\AppointmentConfirmed;
use App\Mail\AppointmentRescheduled;
use App\Mail\ContactMessageReceived;
use App\Models\Appointment;
use App\Models\MailDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class SendMailDelivery implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(public int $deliveryId) {}

    /** @return array<int, int> */
    public function backoff(): array
    {
        return [60, 300];
    }

    /** @return array<int, object> */
    public function middleware(): array
    {
        return [(new WithoutOverlapping('mail-delivery:'.$this->deliveryId))->releaseAfter(10)->expireAfter(60)];
    }

    public function handle(): void
    {
        $delivery = MailDelivery::with(['appointment.treatment', 'contactMessage'])->find($this->deliveryId);
        if (! $delivery || in_array($delivery->status, ['sent', 'skipped'], true)) {
            return;
        }
        $appointment = $delivery->appointment;
        if ($appointment && in_array($delivery->kind, ['confirmed', 'rescheduled'], true)
            && ($appointment->status !== 'confirmed' || $appointment->starts_at->isPast()
                || $appointment->starts_at->toIso8601String() !== $delivery->payload['starts_at'])) {
            $delivery->status = 'skipped';
            $delivery->save();

            return;
        }
        $delivery->status = 'sending';
        $delivery->attempts++;
        $delivery->attempted_at = now();
        $delivery->save();
        try {
            $mail = match ($delivery->kind) {
                'contact' => new ContactMessageReceived($delivery->contactMessage),
                'confirmed' => new AppointmentConfirmed($appointment),
                'rescheduled' => new AppointmentRescheduled($appointment, $delivery->payload['old_starts_at']),
                'cancelled', 'clinic_cancelled' => new AppointmentCancelled($appointment, $delivery->kind === 'clinic_cancelled'),
                default => throw new RuntimeException('Unsupported mail delivery kind.'),
            };
            if (Mail::to($delivery->recipient)->locale($delivery->locale)->send($mail) === null) {
                throw new RuntimeException('Mail transport did not accept the message.');
            }
            $delivery->status = 'sent';
            $delivery->sent_at = now();
            $delivery->save();
            $this->syncAppointment($delivery, 'sent');
        } catch (Throwable $exception) {
            $delivery->status = 'pending';
            $delivery->save();
            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        $delivery = MailDelivery::find($this->deliveryId);
        if ($delivery && ! in_array($delivery->status, ['sent', 'skipped'], true)) {
            $delivery->status = 'failed';
            $delivery->save();
            $this->syncAppointment($delivery, 'failed');
        }
    }

    private function syncAppointment(MailDelivery $delivery, string $status): void
    {
        if (! $delivery->appointment_id || $delivery->kind === 'clinic_cancelled') {
            return;
        }
        $cancelled = $delivery->kind === 'cancelled';
        $query = Appointment::whereKey($delivery->appointment_id)->where('status', $cancelled ? 'cancelled' : 'confirmed');
        if (! $cancelled) {
            $query->where('starts_at', $delivery->payload['starts_at']);
        }
        $data = [
            $cancelled ? 'cancellation_mail_status' : 'mail_status' => $status,
            $cancelled ? 'cancellation_mail_attempted_at' : 'mail_attempted_at' => $delivery->attempted_at,
        ];
        if ($status === 'sent') {
            $data[$cancelled ? 'cancellation_sent_at' : 'confirmation_sent_at'] = $delivery->sent_at;
        }
        $query->update($data);
    }
}
