<?php

namespace App\Services;

use App\Jobs\SendMailDelivery;
use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\MailDelivery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MailDeliveryService
{
    /** @param array<string, string> $payload */
    public function appointment(Appointment $appointment, string $kind, array $payload = [], bool $resend = false): MailDelivery
    {
        return DB::transaction(function () use ($appointment, $kind, $payload, $resend): MailDelivery {
            $current = Appointment::whereKey($appointment->id)->lockForUpdate()->firstOrFail();
            $cancelled = in_array($kind, ['cancelled', 'clinic_cancelled'], true);
            if (($cancelled && $current->status !== 'cancelled') || (! $cancelled && ($current->status !== 'confirmed' || $current->starts_at->isPast()))) {
                throw ValidationException::withMessages(['appointment' => __('management.not_editable')]);
            }
            $payload['starts_at'] = $current->starts_at->toIso8601String();
            $key = $current->reference.':'.$kind.':'.$current->starts_at->timestamp;
            if ($resend) {
                $pending = MailDelivery::where('appointment_id', $current->id)->where('kind', $kind)
                    ->where('payload->starts_at', $payload['starts_at'])
                    ->whereIn('status', ['pending', 'sending'])->latest('id')->first();
                if ($pending) {
                    return $pending;
                }
                $key .= ':'.Str::uuid();
            }
            $delivery = MailDelivery::firstOrCreate(['deduplication_key' => $key], [
                'appointment_id' => $current->id, 'kind' => $kind,
                'recipient' => $kind === 'clinic_cancelled' ? config('contact.mail_to') : $current->email,
                'locale' => $current->locale, 'payload' => $payload,
            ]);
            if ($delivery->wasRecentlyCreated) {
                if ($kind !== 'clinic_cancelled') {
                    $field = $cancelled ? 'cancellation_mail_status' : 'mail_status';
                    $current->{$field} = 'pending';
                    $current->save();
                }
                SendMailDelivery::dispatch($delivery->id)->afterCommit();
            }

            return $delivery;
        });
    }

    public function contact(ContactMessage $message, bool $resend = false): MailDelivery
    {
        return DB::transaction(function () use ($message, $resend): MailDelivery {
            ContactMessage::whereKey($message->id)->lockForUpdate()->firstOrFail();
            $pending = MailDelivery::where('contact_message_id', $message->id)->whereIn('status', ['pending', 'sending'])->first();
            if ($pending) {
                return $pending;
            }
            $delivery = MailDelivery::firstOrCreate([
                'deduplication_key' => 'contact:'.$message->id.($resend ? ':'.Str::uuid() : ''),
            ], [
                'contact_message_id' => $message->id, 'kind' => 'contact',
                'recipient' => config('contact.mail_to'), 'locale' => $message->locale,
            ]);
            if ($delivery->wasRecentlyCreated) {
                SendMailDelivery::dispatch($delivery->id)->afterCommit();
            }

            return $delivery;
        });
    }

    public function retry(MailDelivery $delivery): void
    {
        DB::transaction(function () use ($delivery): void {
            $current = MailDelivery::whereKey($delivery->id)->lockForUpdate()->firstOrFail();
            if ($current->status !== 'failed') {
                throw ValidationException::withMessages(['delivery' => __('operations.retry_unavailable')]);
            }
            $current->status = 'pending';
            $current->save();
            SendMailDelivery::dispatch($current->id)->afterCommit();
        });
    }
}
