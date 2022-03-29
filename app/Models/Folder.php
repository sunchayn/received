<?php

namespace App\Models;

use App\Models\Traits\StorageSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Folder.
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $slug
 * @property User $user
 * @property string|null $password
 * @property Carbon|null $shared_at
 * @property Collection|null $files
 *
 * @mixin Builder
 */
class Folder extends Model
{
    use StorageSize;

    protected $guarded = [];

    protected $dates = [
        'shared_at',
    ];

    protected $with = [
        'files',
    ];

    /**
     * Determine if current folder is shared.
     *
     * @return bool
     */
    public function isShared()
    {
        return $this->shared_at !== null && $this->password !== null;
    }

    /**
     * Get the owner of this folder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the files within this folder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class)->latest();
    }

    /**
     * Check if this folder is owned by the given $user.
     *
     * @param  User  $user
     * @return bool
     */
    public function isOwnedBy(User $user)
    {
        return $this->user_id == $user->id;
    }

    /**
     * Get folder path in bucket.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->user->getBucket().'/'.$this->slug;
    }

    /**
     * Calculate folder size.
     *
     * @return string
     */
    public function getFolderSize()
    {
        $size = $this->files->reduce(function ($carry, $file) {
            /**
             * @var File $file
             */
            $carry += $file->size;

            return $carry;
        });

        return $size ? $this->getSuitableSizeUnit($size) : '0 Bytes';
    }

    // Exporting
    public function toArray()
    {
        $data = parent::toArray();
        $data['size'] = $this->getFolderSize();
        $data['is_shared'] = $this->isShared();
        $data['shared_at'] = $this->shared_at ? $this->shared_at->diffForHumans() : null;

        return $data;
    }
}
