<?php

namespace App\Repositories;

use App\Models\Folder;
use Illuminate\Http\UploadedFile;

/**
 * Class FilesRepository
 * @package App\Repositories
 */
class FilesRepository
{

    /**
     * Create a new file.
     *
     * @param Folder $folder
     * @param UploadedFile $file
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(Folder $folder, UploadedFile $file)
    {
        return $folder->files()->create([
            'filename' => $this->decideFileName($folder, $file),
            'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize() / 1024,
            'type' => $this->getType($file),
        ]);
    }

    /**
     * Determine file type category from its Mime.
     *
     * @param UploadedFile $file
     * @return mixed|string
     */
    private function getType(UploadedFile $file)
    {
        $mime = $file->getMimeType();
        return $mime ? explode('/', $mime)[0] : 'n/a';
    }

    /**
     * Sort out a unique name for the file.
     *
     * @param Folder $folder
     * @param UploadedFile $file
     * @return mixed|string
     */
    private function decideFileName(Folder $folder, UploadedFile $file)
    {
        $filename = pathinfo($file->getClientOriginalName())['filename'];
        $existentFile = $folder->files()->where('filename', $filename)->count() > 0;

        return ! $existentFile ? $filename : $filename.'_'.time();
    }
}
