<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationPrefs
 * @package App\Models
 * @property bool $notify_by_sms
 * @property bool $notify_by_mail
 */
class NotificationPrefs extends Model
{
    protected $guarded = [];

    protected $casts = [
        'notify_by_mail' => 'boolean',
        'notify_by_sms' => 'boolean',
    ];
}
