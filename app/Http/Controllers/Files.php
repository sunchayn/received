<?php

namespace App\Http\Controllers;

use App\Events\BucketUpdated;
use App\Http\Requests\Files\DeleteRequest;
use App\Http\Requests\Files\DownloadRequest;
use App\Models\File;
use Auth;
use Storage;

class Files extends Controller
{
    /**
     * GET /files/download/{file}.
     *
     * Download a given file.
     *
     * @param DownloadRequest $request
     * @param File $file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(DownloadRequest $request, File $file)
    {
        return Storage::disk('buckets')->download($file->getPath());
    }

    /**
     * DELETE /files/{file}.
     *
     * Delete a given file.
     *
     * @param DeleteRequest $request
     * @param File $file
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(DeleteRequest $request, File $file)
    {
        $deleted = Storage::disk('buckets')->delete($file->getPath());

        if (! $deleted) {
            return $this->jsonError('We were unable to delete the file', 500);
        }

        $file->delete();

        event(new BucketUpdated(Auth::user()));

        return $this->empty();
    }
}
