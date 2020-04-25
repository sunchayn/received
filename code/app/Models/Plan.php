<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Plan
 *
 * @property int $id
 *
 * @mixin Builder
 */
class Plan extends Model
{
    protected $guarded = [];

    /**
     * Get default Plan.
     * @return Plan|Model|object|null
     */
    public static function default()
    {
        return self::where('is_default', true)->first();
    }
}
