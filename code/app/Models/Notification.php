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
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Notification extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'is_seen' => 'boolean',
        'is_notified' => 'boolean',
    ];

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

    // Exporter
    public function toArray()
    {
        $data = parent::toArray();

        $data['created_at'] = $this->created_at->diffForHumans();
        return $data;
    }
}
