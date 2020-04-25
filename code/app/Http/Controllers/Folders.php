<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Folder;
use Auth;
use Storage;

class Folders extends Controller
{
    public function all() {
        return $this->jsonData(Auth::user()->folders);
    }

    public function store() {
        $data = request()->validate([
            'name' => 'required',
        ]);

        $folder = Auth::user()->folders()->where('name', $data['name'])->first();

        if ($folder) {
            return $this->validationErrors([
                'name' => 'The folder is already exists',
            ]);
        }

        $data['slug'] = Str::slug($data['name']);
        $folder = Auth::user()->folders()->create($data);

        $bucket = Auth::user()->getBucket();
        if (! Storage::disk('buckets')->has($bucket)) {
            Storage::disk('buckets')->makeDirectory($bucket);
        }

        Storage::disk('buckets')->makeDirectory($bucket . '/' . $folder->slug);

        return $this->jsonData($folder, 201);
    }

    public function save(Folder $folder) {
        if (! $folder->isOwnedBy(Auth::user())) {
            return $this->unauthorized();
        }

        $data = request()->validate([
            'name' => 'required',
        ]);

        $folderWithSameName = Auth::user()->folders()->where('name', $data['name'])->first();

        if ($folderWithSameName && $folderWithSameName->id != $folder->id) {
            return $this->validationErrors([
                'name' => 'The folder is already exists',
            ]);
        }

        $data['slug'] = Str::slug($data['name']);
        $folder->update($data);

        return $this->jsonData($folder);
    }

    public function share(Folder $folder) {
        if (! $folder->isOwnedBy(Auth::user())) {
            return $this->unauthorized();
        }

        $data = request()->validate([
            'password' => 'required|min:6',
        ]);

        $folder->update([
            'password' => bcrypt($data['password']),
            'shared_at' => now(),
        ]);

        return $this->jsonSuccess('The folder has been shared.');
    }

    public function revoke(Folder $folder) {
        if (! $folder->isOwnedBy(Auth::user())) {
            return $this->unauthorized();
        }

        $folder->update([
            'password' => null,
            'shared_at' => null,
        ]);

        return $this->jsonSuccess('The folder access has been revoked.');
    }

    public function delete(Folder $folder) {
        if (! $folder->isOwnedBy(Auth::user())) {
            return $this->unauthorized();
        }

        Storage::disk('buckets')->deleteDirectory(Auth::user()->getBucket() . '/' . $folder->slug);
        $folder->delete();

        return $this->empty();
    }
}
