<?php

namespace Tests\Feature;

use App\Events\BucketUpdated;
use App\Events\FilesUploaded;
use App\Events\UserCreated;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use App\Services\SMS\Provider as SMSProvider;
use Storage;
use Auth;

class EventsPropagationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        SMSProvider::setupFakeService([]);
        Storage::fake('buckets');
    }

    /**
     * @test
     */
    public function it_fire_an_event_upon_users_creation()
    {
        Event::fake();

        $data = factory(User::class)->make();

        $this
            ->ajax('post', route('auth.signup'), $data->toArray())
        ;

        Event::assertDispatched(UserCreated::class);
    }

    /**
     * @test
     */
    public function it_fire_an_event_upon_files_upload()
    {
        Event::fake();

        // Prepare required conditions
        // --
        $this->prepareFolderForUpload($password, $_, $user, 100);

        $data = [
            'password' => $password,
            'files' => [
                UploadedFile::fake()->create('file.jpg', 10),
            ],
        ];

        // Upload files
        // --
        $route = route('send.upload', ['username' => $user->username]);
        $this->ajax('post', $route, $data)->assertOk();

        Event::assertDispatched(FilesUploaded::class);
    }

    /**
     * @test
     */
    public function it_fire_an_event_upon_folder_deletion()
    {
        Event::fake();

        $this->signin();

        $folder = $this->createFolderForUser(Auth::user());

        $this
            ->delete(route('folders.delete', ['folder'=> $folder->id]))
            ->assertNoContent()
        ;

        Event::assertDispatched(BucketUpdated::class);
    }

    /**
     * @test
     */
    public function it_fire_an_event_upon_files_deletion()
    {
        Event::fake();

        $this->signin();

        $file = $this->createFileForUser(Auth::user());

        $this
            ->delete(route('files.delete', ['file'=> $file->id]))
            ->assertNoContent()
        ;

        Event::assertDispatched(BucketUpdated::class);
    }
}
