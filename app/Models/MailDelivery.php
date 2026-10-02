<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailDelivery extends Model
{
    use HasFactory;

    protected $fillable = ['appointment_id', 'contact_message_id', 'kind', 'recipient', 'locale', 'payload', 'deduplication_key'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'attempted_at' => 'immutable_datetime', 'sent_at' => 'immutable_datetime'];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function contactMessage(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class);
    }
}
