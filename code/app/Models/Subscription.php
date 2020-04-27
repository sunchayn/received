<?php

namespace App\Models;

use App\Models\Traits\StorageSize;
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
    use StorageSize;

    protected $guarded = [];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function remainingStorage()
    {
        $size = max($this->plan->storage_limit - $this->used_storage, 0);
        return $this->getSuitableSizeUnit($size);
    }

    public function remainingStorageRaw()
    {
        return max($this->plan->storage_limit - $this->used_storage, 0);
    }
}
