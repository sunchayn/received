<?php

namespace App\Models;

use App\Models\Traits\StorageSize;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Folder
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
 * @mixin \Eloquent
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

    public function isShared()
    {
        return $this->shared_at !== null && $this->password !== null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function isOwnedBy(User $user)
    {
        return $this->user_id == $user->id;
    }

    public function getPath()
    {
        return $this->user->getBucket() . '/' . $this->slug;
    }

    public function getFolderSize($unit = 'kb')
    {
        $size = $this->files->reduce(function ($carry, $file) {
            /**
             * @var File $file
             */

            $carry += $file->size;
            return $carry;
        });

        return $size ? $this->getSuitableSizeUnit($size) : 0;
    }

    // Exporting
    public function toArray()
    {
        $data = parent::toArray();
        $data['size'] = $this->getFolderSize('mb');
        $data['is_shared'] = $this->isShared();

        return $data;
    }
}
