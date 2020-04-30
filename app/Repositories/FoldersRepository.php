<?php

namespace App\Repositories;

use App\Events\BucketUpdated;
use App\Models\Folder;
use App\Models\Subscription;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Storage;

/**
 * Class FoldersRepository
 * @package App\Repositories
 */
class FoldersRepository
{
    /**
     * Create a folder.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        $data['slug'] = Str::slug($data['name']);

        /**
         * @var Folder $folder
         */

        $folder = Auth::user()->folders()->create($data);

        $bucket = $this->createUserBucketIfNotExists();
        $this->createFolderStorageEndpoint($bucket, $folder);

        return $folder;
    }

    /**
     * Update a folder
     *
     * @param Folder $folder
     * @param array $data
     * @return Folder
     */
    public function update(Folder $folder, array $data)
    {
        $data['slug'] = Str::slug($data['name']);

        $this->renameFolder($folder->slug, $data['slug']);

        $folder->update($data);

        return $folder;
    }

    /**
     * Share a folder.
     *
     * @param Folder $folder
     * @param array $data
     * @return Folder
     */
    public function share(Folder $folder, array $data)
    {
        $folder->update([
            'password' => bcrypt($data['password']),
            'shared_at' => now(),
        ]);

        return $folder;
    }

    /**
     * Change folder password.
     *
     * @param Folder $folder
     * @param array $data
     * @return Folder
     */
    public function changePassword(Folder $folder, array $data)
    {
        $folder->update([
            'password' => bcrypt($data['password']),
        ]);

        return $folder;
    }

    /**
     * Revoke folder access.
     *
     * @param Folder $folder
     * @return Folder
     */
    public function revoke(Folder $folder)
    {
        $folder->update([
            'password' => null,
            'shared_at' => null,
        ]);

        return $folder;
    }

    /**
     * Delete a folder.
     *
     * @param Folder $folder
     * @return bool
     */
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

    /**
     * Find the folder identified by the given $password.
     *
     * @param User $user
     * @param string $password
     * @return Fodler|mixed|null
     */
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

    /**
     * Create a user bucket if it does not exists.
     *
     * @return string
     */
    private function createUserBucketIfNotExists()
    {
        $bucket = Auth::user()->getBucket();
        if (! Storage::disk('buckets')->has($bucket)) {
            Storage::disk('buckets')->makeDirectory($bucket);
        }

        return $bucket;
    }

    /**
     * Create physical folder in the bucket.
     *
     * @param string $bucket
     * @param Folder $folder
     */
    private function createFolderStorageEndpoint(string $bucket, Folder $folder)
    {
        Storage::disk('buckets')->makeDirectory($bucket.'/'.$folder->slug);
    }

    /**
     * Rename a folder in the bucket.
     *
     * @param $old
     * @param $new
     */
    private function renameFolder($old, $new)
    {
        if ($old === $new) {
            return;
        }

        $bucket = Auth::user()->getBucket();
        Storage::disk('buckets')->move($bucket.'/'.$old, $bucket.'/'.$new);
    }

    /**
     * Delete a folder from the bucket.
     *
     * @param Folder $folder
     * @return bool
     */
    private function deleteDirectory(Folder $folder)
    {
        return Storage::disk('buckets')->deleteDirectory(Auth::user()->getBucket().'/'.$folder->slug);
    }

    /**
     * Determine if the folder owner has enough storage to accept more files or not.
     *
     * @param $files
     * @param Subscription $subscription
     * @return bool
     */
    public function canUploadFiles($files, Subscription $subscription)
    {
        $size = $this->getUploadedFilesSize($files);

        return $subscription->remainingStorageRaw() > $size;
    }

    /**
     * Calculate the size of the POSTed files.
     *
     * @param $files
     * @return float|int
     */
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

    /**
     * Store files in the bucket.
     *
     * @param $files
     * @param Folder $folder
     * @return array
     */
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

    /**
     * Create a ZIP archive for the folder.
     *
     * @param Folder $folder
     * @return string
     */
    public function zip(Folder $folder)
    {
        // Zipped folder are stored in folder organized by dates to delete them later after a specific period
        // todo: create a cron job to delete old zipped folders.
        $downloadFolder = Carbon::today()->toDateString();

        if (Storage::disk('download')->missing($downloadFolder)) {
            Storage::disk('download')->makeDirectory($downloadFolder);
        }

        chdir(Storage::disk('download')->path($downloadFolder));

        $filename = $folder->slug.'.zip';

        $zip = new \ZipArchive();
        $zip->open($filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $folderPath = Storage::disk('buckets')->path($folder->getPath());
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folderPath));

        foreach ($files as $name => $file) {
            if (! $file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = './'.basename($filePath);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return Storage::disk('download')->path($downloadFolder.'/'.$filename);
    }
}
