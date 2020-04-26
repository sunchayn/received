<?php

namespace App\Repositories;

use App\Models\Folder;
use Illuminate\Http\UploadedFile;

class FilesRepository
{
    public function create(Folder $folder, UploadedFile $file)
    {
        return $folder->files()->create([
            'filename' => $this->decideFileName($folder, $file),
            'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize() / 1024,
            'type' => $this->getType($file),
        ]);
    }

    private function getType(UploadedFile $file)
    {
        $mime = $file->getMimeType();
        return $mime ? explode('/', $mime)[0] : 'n/a';
    }

    private function decideFileName(Folder $folder, UploadedFile $file)
    {
        $filename = pathinfo($file->getClientOriginalName())['filename'];
        $existentFile = $folder->files()->where('filename', $filename)->first();

        return !$existentFile ? $filename : $filename . '_' . time();
    }
}
