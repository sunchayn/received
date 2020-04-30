<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notification.
 *
 * @property bool is_seen
 * @property bool is_notified
 * @property Carbon created_at
 * @property User $user
 *
 * @mixin Builder
 */
class Notification extends Model
{
    public const TYPE_RECEIVED_FILES = 'RECEIVED_FILES';
    public const EMAIL = 'is_notified_by_mail';
    public const SMS = 'is_notified_by_sms';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'is_seen' => 'boolean',
        'is_notified_by_sms' => 'boolean',
        'is_notified_by_mail' => 'boolean',
    ];

    /**
     * Get notification subject.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get only the undelivered notifications for the given $channel.
     *
     * @param string $channel
     * @return Notification
     */
    public static function notNotified(string $channel)
    {
        return self::where('is_seen', false)
            ->where($channel, false);
    }

    /**
     * Determine whether the notification is seen or not.
     *
     * @return bool
     */
    public function isSeen()
    {
        return $this->is_seen;
    }

    /**
     * Normalize notification data.
     *
     * @param $data
     */
    public function setDataAttribute($data): void
    {
        $this->attributes['data'] = json_encode($data);
    }

    /**
     * Normalize notification data.
     *
     * @param $value
     * @return mixed
     */
    public function getDataAttribute($value)
    {
        return json_decode($value);
    }

    // Exporter
    public function toArray()
    {
        $data = parent::toArray();
        $data['created_at'] = $this->created_at->diffForHumans();

        return $data;
    }
}
