<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_id',
        'starts_at',
        'ends_at',
        'name',
        'email',
        'phone',
        'birth_date',
        'gender',
        'reason',
        'notes',
        'is_new_patient',
        'locale',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
            'birth_date' => 'immutable_date',
            'is_new_patient' => 'boolean',
            'mail_attempted_at' => 'immutable_datetime',
            'confirmation_sent_at' => 'immutable_datetime',
            'cancellation_mail_attempted_at' => 'immutable_datetime',
            'cancellation_sent_at' => 'immutable_datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment): void {
            $appointment->reference ??= (string) Str::uuid();
        });
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }
}
