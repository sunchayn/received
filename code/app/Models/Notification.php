<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class Notification
 * @package App\Models
 * @property bool is_seen
 * @property bool is_notified
 * @property Carbon created_at
 * @property User $user
 * @mixin \Illuminate\Database\Eloquent\Builder
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

    public static function notNotified(string $channel)
    {
        return self::where('is_seen', false)
            ->where($channel, false)
        ;
    }

    /**
     * Determine whether the notification is seen or not.
     * @return bool
     */
    public function isSeen()
    {
        return $this->is_seen;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setDataAttribute($data):void
    {
        $this->attributes['data'] = json_encode($data);
    }

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
