<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Subscription
 *
 * @property int $id
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
}
