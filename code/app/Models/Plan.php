<?php

namespace App\Models;

use App\Models\Traits\StorageSize;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Plan
 *
 * @property int $id
 * @property int $storage_limit
 * @property Carbon $created_at
 * @mixin Builder
 */
class Plan extends Model
{
    use StorageSize;

    protected $guarded = [];

    /**
     * Get default Plan.
     * @return Plan|Model|object|null
     */
    public static function default()
    {
        return self::where('is_default', true)->first();
    }

    // Exporter
    public function toArray()
    {
        $data = parent::toArray();
        $data['created_at'] = $this->created_at->diffForHumans();
        $data['storage_limit'] = $this->getSuitableSizeUnit($this->storage_limit);

        return $data;
    }
}
