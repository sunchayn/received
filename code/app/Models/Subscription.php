<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Subscription
 *
 * @property int $id
 * @property int $used_storage
 * @property Plan $plan
 *
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    protected $guarded = [];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function remainingStorage()
    {
        return max($this->plan->storage_limit - $this->used_storage, 0);
    }
}
