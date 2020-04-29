<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;
use Tests\TestCase;

class FilesManagementTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('buckets');
    }

    /**
     * @test
     */
    public function user_can_download_his_files()
    {
        $this->signin();

        // Downloading his file is Allowed
        // --
        $file = $this->createFileForUser(Auth::user());

        $this
            ->get(route('files.download', ['file'=> $file->id]))
            ->assertHeader('Content-Disposition', 'attachment; filename='.$file->getQualifiedFilename());

        // Downloading another user file is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->create([
            'user_id' => $anotherUser->id,
        ]);

        $file = factory(File::class)->create([
            'folder_id' => $folder->id,
        ]);

        $this
            ->get(route('files.download', ['file'=> $file->id]))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function user_can_delete_his_files()
    {
        $this->signin();

        // Deleting his file is Allowed
        // --
        $file = $this->createFileForUser(Auth::user());

        $this
            ->delete(route('files.delete', ['file'=> $file->id]))
            ->assertNoContent();

        Storage::disk('buckets')->assertMissing($file->getPath());

        // Deleting another user folder is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->create([
            'user_id' => $anotherUser->id,
        ]);

        $file = factory(File::class)->create([
            'folder_id' => $folder->id,
        ]);

        $this
            ->delete(route('files.delete', ['file'=> $file->id]))
            ->assertForbidden();
    }
}
