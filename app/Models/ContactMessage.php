<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'locale',
    ];

    public function latestDelivery(): HasOne
    {
        return $this->hasOne(MailDelivery::class)->latestOfMany();
    }
}
