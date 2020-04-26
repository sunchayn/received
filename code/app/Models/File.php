<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 * @package App\Models
 *
 * @property string $filename
 * @property string $extension
 * @property Folder $folder
 */
class File extends Model
{
    protected $guarded = [];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function getPath()
    {
        return $this->folder->getPath() . '/' . $this->filename . '.' . $this->extension;
    }
}
