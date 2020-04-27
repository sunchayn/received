<?php

namespace App\Http\Controllers\Send;

use App\Events\FilesUploaded;
use App\Http\Requests\Send\FilesUploadRequest;
use App\Http\Requests\Send\FolderUnlockRequest;
use App\Models\User;
use App\Models\Folder;
use App\Repositories\FoldersRepository;
use App\Repositories\UsersRepository;

class Folders extends \App\Http\Controllers\Controller
{

    /**
     * POST /send/{username}/unlock
     *
     * Check folder availability for upload
     *
     * @param FolderUnlockRequest $request
     * @param FoldersRepository $folders
     * @param UsersRepository $users
     * @param $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlock(
        FolderUnlockRequest $request,
        FoldersRepository $folders,
        UsersRepository $users,
        $username
    ) {
        $user = $users->getByUsername($username);

        /**
         * @var Folder $folder
         */
        $password = $request->validated()['password'];
        $folder = $folders->getFolderByPassword($user, $password);

        if (! $folder) {
            return $this->validationErrors([
                'password' => ['There\'s no public bucket matching the given credentials.'],
            ]);
        }

        // It does not allow uploading files to private folders
        if (! $folder->isShared()) {
            return $this->forbidden();
        }

        // It does not allow uploading files to users without subscription
        if (!$folder->user->subscription) {
            return $this->validationErrors([
                'size' => ['This bucket does not have enough storage space to accept more files.'],
            ]);
        }

        return $this->jsonData([
            'remainingStorage' => $folder->user->subscription->remainingStorage(),
        ]);
    }

    /**
     * POST /send/{username}/upload
     *
     * Upload files to the specified folder by password.
     *
     * @param FilesUploadRequest $request
     * @param FoldersRepository $folders
     * @param UsersRepository $users
     * @param $username
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(
        FilesUploadRequest $request,
        FoldersRepository $folders,
        UsersRepository $users,
        $username
    ) {
        $data = $request->validated();

        /**
         * @var User $user
         */
        $user = $users->getByUsername($username);

        /**
         * @var Folder $folder
         */
        $folder = $folders->getFolderByPassword($user, $data['password']);

        if (! $folder) {
            return $this->validationErrors([
                'password' => ['There\'s no public bucket matching the given credentials.'],
            ]);
        }

        // It does not allow uploading files to users without subscription
        if (! $user->subscription) {
            return $this->validationErrors([
                'size' => ['This bucket does not have enough storage space to accept more files.'],
            ], [
                'code' => 1
            ]);
        }

        // It does not allow uploading files to invalid folders
        if (! $folders->canUploadFiles($data['files'], $user->subscription)) {
            return $this->validationErrors([
                'size' => ['This bucket does not have enough storage space to accept more files.'],
            ], [
                'remaining_storage' => $user->subscription->remainingStorage(),
                'code' => 2
            ]);
        }

        $files = $folders->uploadFiles($data['files'], $folder);

        event(new FilesUploaded($files, $user));

        return $this->jsonSuccess('The files has been successfully uploaded.');
    }
}
