<?php

namespace App\Models;

use App\Models\Traits\StorageSize;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\File.
 *
 * @property string $filename
 * @property string $extension
 * @property int $size
 * @property Folder $folder
 * @property Carbon $created_at
 *
 * @mixin \Eloquent
 */
class File extends Model
{
    use StorageSize;

    protected $guarded = [];

    /**
     * Get file parent folder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Get the concatenation result of filename and extension.
     *
     * @return string
     */
    public function getQualifiedFilename()
    {
        return $this->filename.'.'.$this->extension;
    }

    /**
     * Get file path in the bucket.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->folder->getPath().'/'.$this->getQualifiedFilename();
    }

    // Exporting
    public function toArray()
    {
        $data = parent::toArray();
        $data['sent_on'] = $this->created_at->diffForHumans();
        $data['size'] = $this->getSuitableSizeUnit($this->size);

        return $data;
    }
}
