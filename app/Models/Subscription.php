<?php

namespace App\Models;

use App\Models\Traits\StorageSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Subscription.
 *
 * @property int $id
 * @property int $used_storage
 * @property Plan $plan
 *
 * @mixin Builder
 */
class Subscription extends Model
{
    use StorageSize;

    protected $guarded = [];

    /**
     * Get subscription Plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Calculate the remaining storage.
     *
     * @return string
     */
    public function remainingStorage()
    {
        return $this->getSuitableSizeUnit($this->remainingStorageRaw());
    }

    /**
     * Calculate the remaining storage without formatting.
     *
     * @return mixed
     */
    public function remainingStorageRaw()
    {
        return max($this->plan->storage_limit - $this->used_storage, 0);
    }
}
