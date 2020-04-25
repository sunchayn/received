<?php

namespace App\Repositories;

use App\Models\Folder;
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
        $this->deleteDirectory($folder);
        $folder->delete();
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
        Storage::disk('buckets')->deleteDirectory(Auth::user()->getBucket() . '/' . $folder->slug);
    }
}