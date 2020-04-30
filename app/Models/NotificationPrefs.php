<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notification.
 *
 * @property bool $notify_by_sms
 * @property bool $notify_by_mail
 *
 * @mixin Builder
 */
class NotificationPrefs extends Model
{
    protected $guarded = [];

    protected $casts = [
        'notify_by_mail' => 'boolean',
        'notify_by_sms' => 'boolean',
    ];
}
