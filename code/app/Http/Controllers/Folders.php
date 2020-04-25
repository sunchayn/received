<?php

namespace App\Http\Controllers;

use App\Http\Requests\Folders\CreateRequest;
use App\Http\Requests\Folders\DeleteRequest;
use App\Http\Requests\Folders\RevokeRequest;
use App\Http\Requests\Folders\ShareRequest;
use App\Http\Requests\Folders\UpdateRequest;
use App\Repositories\FoldersRepository;
use App\Models\Folder;
use Auth;

class Folders extends Controller
{
    /**
     * GET /folders/all
     *
     * Return a JSON array of current user folders.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        return $this->jsonData(Auth::user()->folders);
    }

    /**
     * POST /folders
     *
     * Create a new folder
     *
     * @param CreateRequest $request
     * @param FoldersRepository $folders
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRequest $request, FoldersRepository $folders)
    {
        $folder = $folders->create($request->validated());
        return $this->jsonData($folder, 201);
    }

    /**
     * PATCH /folders/edit/{folder}
     *
     * Update a given folder
     *
     * @param UpdateRequest $request
     * @param Folder $folder
     * @param FoldersRepository $folders
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(UpdateRequest $request, FoldersRepository $folders, Folder $folder)
    {
        $folder = $folders->update($folder, $request->validated());
        return $this->jsonData($folder);
    }

    /**
     * PATCH /folders/share/{folder}
     *
     * Share a given folder
     *
     * @param ShareRequest $request
     * @param Folder $folder
     * @param FoldersRepository $folders
     * @return \Illuminate\Http\JsonResponse
     */
    public function share(ShareRequest $request, FoldersRepository $folders, Folder $folder)
    {
        $folders->share($folder, $request->validated());
        return $this->jsonSuccess('The folder has been shared.');
    }

    /**
     * PATCH /folders/revoke/{folder}
     *
     * @param RevokeRequest $request
     * @param FoldersRepository $folders
     * @param Folder $folder
     * @return \Illuminate\Http\JsonResponse
     */
    public function revoke(RevokeRequest $request, FoldersRepository $folders, Folder $folder)
    {
        $folders->revoke($folder);
        return $this->jsonSuccess('The folder access has been revoked.');
    }

    /**
     * DELETE /folders/{folder}
     *
     * Delete a given folder
     *
     * @param DeleteRequest $request
     * @param FoldersRepository $folders
     * @param Folder $folder
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteRequest $request, FoldersRepository $folders, Folder $folder)
    {
        $folders->delete($folder);
        return $this->empty();
    }
}
