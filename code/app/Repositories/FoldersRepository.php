<?php

namespace App\Repositories;

use App\Events\BucketUpdated;
use App\Models\Folder;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Storage;
use Auth;

class FoldersRepository
{
    public function create(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $folder = Auth::user()->folders()->create($data);

        $bucket = $this->createUserBucketIfNotExists();
        $this->createFolderStorageEndpoint($bucket, $folder);

        return $folder;
    }

    public function update(Folder $folder, array $data)
    {
        $data['slug'] = Str::slug($data['name']);

        $this->renameFolder($folder->slug, $data['slug']);

        $folder->update($data);

        return $folder;
    }

    public function share(Folder $folder, array $data)
    {
        $folder->update([
            'password' => bcrypt($data['password']),
            'shared_at' => now(),
        ]);

        return $folder;
    }

    public function changePassword(Folder $folder, array $data)
    {
        $folder->update([
            'password' => bcrypt($data['password']),
        ]);

        return $folder;
    }

    public function revoke(Folder $folder)
    {
        $folder->update([
            'password' => null,
            'shared_at' => null,
        ]);

        return $folder;
    }

    public function delete(Folder $folder)
    {
        if ($this->deleteDirectory($folder)) {
            $folder->delete();
            event(new BucketUpdated(Auth::user()));
            return true;
        } else {
            return false;
        }
    }

    public function getFolderByPassword(User $user, string $password)
    {
        $result = null;

        /**
         * @var Fodler $folder
         */
        foreach ($user->shared as $folder) {
            if (\Hash::check($password, $folder->password)) {
                $result = $folder;
                break;
            }
        }

        return $result;
    }

    // Inner Helpers
    // --

    private function createUserBucketIfNotExists()
    {
        $bucket = Auth::user()->getBucket();
        if (! Storage::disk('buckets')->has($bucket)) {
            Storage::disk('buckets')->makeDirectory($bucket);
        }

        return $bucket;
    }

    private function createFolderStorageEndpoint(string $bucket, Folder $folder)
    {
        Storage::disk('buckets')->makeDirectory($bucket . '/' . $folder->slug);
    }

    private function renameFolder($old, $new)
    {
        $bucket = Auth::user()->getBucket();
        Storage::disk('buckets')->move($bucket . '/' . $old, $bucket . '/' . $new);
    }

    private function deleteDirectory(Folder $folder)
    {
        return Storage::disk('buckets')->deleteDirectory(Auth::user()->getBucket() . '/' . $folder->slug);
    }

    public function canUploadFiles($files, Subscription $subscription)
    {
        $size = $this->getUploadedFilesSize($files);
        return $subscription->remainingStorageRaw() > $size;
    }

    public function getUploadedFilesSize($files)
    {
        $size = 0;

        /**
         * @var UploadedFile $file
         */
        foreach ($files as $file) {
            $size += $file->getSize() / 1024;
        }

        return $size;
    }

    public function uploadFiles($files, Folder $folder)
    {
        $entities = [];

        $filesRepository = new FilesRepository();

        foreach ($files as $file) {
            $entities[] = ($entity = $filesRepository->create($folder, $file));
            $file->storeAs($folder->getPath(), "{$entity->filename}.{$entity->extension}", 'buckets');
        }

        return $entities;
    }

    public function zip(Folder $folder)
    {
        // Zipped folder are stored in folder organized by dates to delete them later after a specific period
        // todo: create a cron job to delete old zipped folders.
        $downloadFolder = Carbon::today()->toDateString();

        if (Storage::disk('download')->missing($downloadFolder)) {
            Storage::disk('download')->makeDirectory($downloadFolder);
        }

        chdir(Storage::disk('download')->path($downloadFolder));

        $filename = $folder->slug . '.zip';

        $zip = new \ZipArchive();
        $zip->open($filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $folderPath = Storage::disk('buckets')->path($folder->getPath());
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folderPath));

        foreach ($files as $name => $file) {
            if (! $file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = './' . basename($filePath);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return Storage::disk('download')->path($downloadFolder . '/' . $filename);
    }
}
