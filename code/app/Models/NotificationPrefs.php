<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPrefs extends Model
{
    protected $guarded = [];

    protected $casts = [
        'notify_by_mail' => 'boolean',
        'notify_by_sms' => 'boolean',
    ];
}
