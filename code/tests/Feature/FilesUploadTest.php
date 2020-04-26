<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Folder;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Storage;

class FilesUploadTest extends TestCase
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
    public function it_fetch_proper_folder_with_password()
    {
        $password = '123456';

        $user = factory(User::class)->state('with_subscription')->create();

        factory(Folder::class)->create([
            'user_id' => $user->id,
            'password' => bcrypt($password),
            'shared_at' => now(),
        ]);

        $data = [ 'password' => $password ];

        $route = route('send.unlock', ['username' => $user->username]);

        $this
            ->ajax('post', $route, $data)
            ->assertOk()
        ;
    }

    /**
     * @test
     */
    public function it_shows_proper_error_when_password_is_invalid()
    {
        $password = '123456';

        $user = factory(User::class)->state('with_subscription')->create();

        factory(Folder::class)->create([
            'user_id' => $user->id,
            'password' => bcrypt('0000@@@'),
            'shared_at' => now(),
        ]);

        $data = [ 'password' => $password ];

        $route = route('send.unlock', ['username' => $user->username]);

        $this
            ->ajax('post', $route, $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ])
        ;
    }

    /**
     * @dataProvider file_uploading_data_provider
     * @param $files
     * @param $expectedSize
     * @test
     */
    public function it_properly_upload_files_to_folder($files, $expectedSize)
    {
        // Prepare required conditions
        // --
        $storageLimit = $expectedSize + 100;
        $this->prepareFolderForUpload($password, $folder, $user, $storageLimit);

        $data = [
            'password' => $password,
            'files' => $files,
        ];

        // Upload files
        // --
        $route = route('send.upload', ['username' => $user->username]);
        $this
            ->ajax('post', $route, $data)
            ->assertOk()
        ;

        # Size is properly calculated
        $this->assertEquals($expectedSize, $user->subscription->refresh()->used_storage);

        /**
         * @var File $file
         */

        # Files do exists
        $this->assertEquals(count($files), $folder->files->count());
        foreach ($folder->files as $file) {
            Storage::disk('buckets')->assertExists($file->getPath());
        }
    }

    /**
     * @dataProvider file_uploading_data_provider
     * @param $files
     * @param $expectedSize
     * @test
     */
    public function it_does_not_approve_uploads_if_the_storage_limit_is_achieved($files, $expectedSize)
    {
        // Prepare required conditions
        // --
        $storageLimit = $expectedSize - 100;

        $this->prepareFolderForUpload($password, $folder, $user, $storageLimit);

        $data = [
            'password' => $password,
            'files' => $files,
        ];

        // Upload files
        // --
        $route = route('send.upload', ['username' => $user->username]);
        $this
            ->ajax('post', $route, $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'size',
            ])
        ;
    }

    // Data providers
    // --
    public function file_uploading_data_provider()
    {
        return [
            [
                [
                    UploadedFile::fake()->createWithContent('image.jpg', 'n\a'),
                    UploadedFile::fake()->createWithContent('document.pdf', 'n\a'),
                    UploadedFile::fake()->createWithContent('video.mp4', 'n\a'),
                    UploadedFile::fake()->createWithContent('text.txt', 'n\a'),
                ],
                12
            ],

            [
                [
                    UploadedFile::fake()->createWithContent('image.jpg', 'n\a'),
                    UploadedFile::fake()->createWithContent('other.psd', 'n\a'),
                ],
                6
            ]
        ];
    }
}
