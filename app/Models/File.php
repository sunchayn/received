<?php

namespace App\Models;

use App\Models\Traits\StorageSize;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class File.
 *
 * @property string $filename
 * @property string $extension
 * @property int $size
 * @property Folder $folder
 * @property Carbon $created_at
 */
class File extends Model
{
    use StorageSize;

    protected $guarded = [];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function getQualifiedFilename()
    {
        return $this->filename.'.'.$this->extension;
    }

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
