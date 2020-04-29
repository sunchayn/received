<?php

namespace App\Http\Controllers;

use App\Http\Requests\Folders\CreateRequest;
use App\Http\Requests\Folders\DeleteRequest;
use App\Http\Requests\Folders\DownloadRequest;
use App\Http\Requests\Folders\PasswordChangingRequest;
use App\Http\Requests\Folders\RevokeRequest;
use App\Http\Requests\Folders\ShareRequest;
use App\Http\Requests\Folders\UpdateRequest;
use App\Models\Folder;
use App\Repositories\FoldersRepository;
use Auth;

class Folders extends Controller
{
    /**
     * GET /folders/all.
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
     * POST /folders.
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
     * PATCH /folders/edit/{folder}.
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
     * PATCH /folders/share/{folder}.
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
        $data = $request->validated();

        $folderWithTheSamePassword = $folders->getFolderByPassword(Auth::user(), $data['password']);

        if ($folderWithTheSamePassword) {
            return $this->validationErrors([
                'password' => ['This password is already used with another folder'],
            ]);
        }

        $folders->share($folder, $data);

        return $this->jsonSuccess('The folder has been shared.');
    }

    /**
     * PATCH /folders/change_password/{folder}.
     *
     * Change a given folder's password
     *
     * @param PasswordChangingRequest $request
     * @param FoldersRepository $folders
     * @param Folder $folder
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(PasswordChangingRequest $request, FoldersRepository $folders, Folder $folder)
    {
        $folders->changePassword($folder, $request->validated());

        return $this->jsonSuccess('The folder has been updated.');
    }

    /**
     * PATCH /folders/revoke/{folder}.
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
     * DELETE /folders/{folder}.
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
        $deleted = $folders->delete($folder);

        if (! $deleted) {
            return $this->jsonError('We were unable to delete the folder', 500);
        }

        return $this->empty();
    }

    /**
     * GET /folders/download/{folder}.
     *
     * Download a given folder
     *
     * @param DownloadRequest $request
     * @param FoldersRepository $folders
     * @param Folder $folder
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(DownloadRequest $request, FoldersRepository $folders, Folder $folder)
    {
        $zip = $folders->zip($folder);

        return response()->download($zip);
    }
}
