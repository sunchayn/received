<?php

namespace Tests\Feature;

use App\Models\Folder;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

class FoldersManagementTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('buckets');
        Storage::fake('download');
    }

    /**
     * @test
     */
    public function user_can_fetch_his_folders()
    {
        $user = $this->signin();

        $foldersCount = 5;
        factory(Folder::class, $foldersCount)->create([
            'user_id' => $user->id,
        ]);

        // Create random extra folders for another User
        // --
        $randomUser = factory(User::class)->create();
        factory(Folder::class, 10)->create([
            'user_id' => $randomUser->id,
        ]);

        $this
            ->json('get', route('folders.all'))
            ->assertStatus(200)
            ->assertJsonCount($foldersCount);
    }

    /**
     * @dataProvider folder_valid_data
     *
     * @param $data
     * @test
     */
    public function user_can_create_a_folder($data)
    {
        $this->signin();

        $this
            ->post(route('folders.create'), $data)
            ->assertStatus(201);

        $this->assertTrue(Auth::user()->folders->isNotEmpty());

        // Folder is created
        // --
        $bucket = Auth::user()->getBucket();
        $folder = Auth::user()->folders->first();
        $this->assertTrue(Storage::disk('buckets')->has($bucket.'/'.$folder->slug));
    }

    /**
     * @dataProvider folder_valid_data
     *
     * @param $data
     * @test
     */
    public function it_create_user_bucket_when_it_does_not_exists($data)
    {
        $this->signin();

        $this->assertFalse(Storage::disk('buckets')->has(Auth::user()->getBucket()));

        $this
            ->post(route('folders.create'), $data);

        $this->assertTrue(Storage::disk('buckets')->has(Auth::user()->getBucket()));
    }

    /**
     * @dataProvider folder_valid_data
     *
     * @param $data
     * @test
     */
    public function it_create_folder_in_storage($data)
    {
        $this->signin();

        $this
            ->post(route('folders.create'), $data);

        $bucket = Auth::user()->getBucket();
        $folder = Auth::user()->folders->first();

        $this->assertTrue(Storage::disk('buckets')->has($bucket.'/'.$folder->slug));
    }

    /**
     * @dataProvider folder_valid_data
     *
     * @param $data
     * @test
     */
    public function it_does_not_allow_unverified_users_to_create_folder($data)
    {
        $user = factory(User::class)->state('not_verified')->create();
        $this->signin($user);

        $this
            ->post(route('folders.create'), $data)
            ->assertStatus(302);

        $this->assertTrue(Auth::user()->folders->isEmpty());
    }

    /**
     * @dataProvider folder_invalid_data
     *
     * @param $data
     * @param $expectedErrors
     * @test
     */
    public function it_does_not_allow_invalid_data_when_creating_folders($data, $expectedErrors)
    {
        $this->signin();

        $this
            ->json('post', route('folders.create'), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors($expectedErrors);
    }

    /**
     * @dataProvider folder_valid_data
     *
     * @param $data
     * @test
     *
     * @throws \Exception
     */
    public function it_does_not_allow_multiple_folders_with_the_same_name($data)
    {
        $this->signin();

        // Folder with the same name for current user
        // --
        factory(Folder::class)->create([
            'user_id' => Auth::id(),
            'name' => $data['name'],
        ]);

        $this
            ->json('post', route('folders.create'), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
            ]);

        // Folder with the same name for another user
        // --

        // Delete the previously deleted folder
        Auth::user()->folders()->first()->delete();

        factory(Folder::class)->create([
            'user_id' => factory(User::class)->create()->id,
            'name' => $data['name'],
        ]);

        $this
            ->post(route('folders.create'), $data)
            ->assertCreated();
    }

    /**
     * @test
     */
    public function user_can_share_a_folder()
    {
        $this->signin();

        $data = [
            'password' => '123456',
        ];

        // Sharing his folder is Allowed
        // --
        $folder = factory(Folder::class)->create([
            'user_id' => Auth::id(),
        ]);

        $this
            ->ajax('patch', route('folders.share', ['folder' => $folder->id]), $data)
            ->assertOk();

        $this->assertTrue($folder->refresh()->isShared());

        // Sharing another user folder is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->create([
            'user_id' => $anotherUser->id,
        ]);

        $this
            ->ajax('patch', route('folders.share', ['folder' => $folder->id]), $data)
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function user_cant_use_the_same_password_with_two_folders()
    {
        $this->signin();

        $password = '123456';

        $data = [
            'password' => $password,
        ];

        // Folder with the same password
        factory(Folder::class)->create([
            'user_id' => Auth::id(),
            'password' => bcrypt($password),
            'shared_at' => Carbon::now(),
        ]);

        // New folder to be shared
        $folder = factory(Folder::class)->create([
            'user_id' => Auth::id(),
        ]);

        $this
            ->ajax('patch', route('folders.share', ['folder' => $folder->id]), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    /**
     * @test
     */
    public function user_can_change_folder_password()
    {
        $this->signin();

        $data = [
            'password' => '123456',
        ];

        // Changing his folder's password is Allowed
        // --
        $folder = factory(Folder::class)->create([
            'user_id' => Auth::id(),
        ]);

        $this
            ->ajax('patch', route('folders.changePassword', ['folder' => $folder->id]), $data)
            ->assertOk();

        $this->assertTrue(\Hash::check($data['password'], $folder->refresh()->password));

        // Changing another user folder's password is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->create([
            'user_id' => $anotherUser->id,
        ]);

        $this
            ->ajax('patch', route('folders.changePassword', ['folder' => $folder->id]), $data)
            ->assertForbidden();
    }

    /**
     * @dataProvider folder_valid_data
     *
     * @param $data
     * @test
     */
    public function user_can_update_his_folder($data)
    {
        $this->signin();
        $bucket = Auth::user()->getBucket();

        // Updating his folder is Allowed
        // --
        $folder = factory(Folder::class)->create([
            'user_id' => Auth::id(),
        ]);

        $oldSlug = $folder->slug;
        Storage::disk('buckets')->makeDirectory($bucket.'/'.$oldSlug);

        $this
            ->ajax('patch', route('folders.edit', ['folder' => $folder->id]), $data)
            ->assertOk();

        $folder->refresh();

        $this->assertNewDataIsPersisted($data, $folder);

        // Folder is renamed
        $this->assertfalse(Storage::disk('buckets')->has($bucket.'/'.$oldSlug));
        $this->assertTrue(Storage::disk('buckets')->has($bucket.'/'.$folder->slug));

        // Sharing another user folder is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->create([
            'user_id' => $anotherUser->id,
        ]);

        $this
            ->ajax('patch', route('folders.edit', ['folder' => $folder->id]), $data)
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function user_can_revoke_sharing_access()
    {
        $this->signin();

        // Revoking his folder is Allowed
        // --
        $folder = factory(Folder::class)->state('shared')->create([
            'user_id' => Auth::id(),
        ]);

        $this
            ->ajax('patch', route('folders.revoke', ['folder'=> $folder->id]))
            ->assertOk();

        $this->assertFalse($folder->refresh()->isShared());

        // Revoking another user's folder is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->state('shared')->create([
            'user_id' => $anotherUser->id,
        ]);

        $this
            ->ajax('patch', route('folders.revoke', ['folder'=> $folder->id]))
            ->assertForbidden();

        $this->assertTrue($folder->refresh()->isShared());
    }

    /**
     * @test
     */
    public function user_can_delete_his_folder()
    {
        $this->signin();

        // Deleting his folder is Allowed
        // --
        $folder = factory(Folder::class)->state('shared')->create([
            'user_id' => Auth::id(),
        ]);

        Storage::disk('buckets')->makeDirectory(Auth::user()->getBucket().'/'.$folder->slug);

        $this
            ->ajax('delete', route('folders.delete', ['folder'=> $folder->id]))
            ->assertNoContent();

        //# Folder is deleted from storage
        //# --
        $this->assertFalse(Storage::disk('buckets')->has(Auth::user()->getBucket().'/'.$folder->slug));

        // Deleting another user's folder is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->state('shared')->create([
            'user_id' => $anotherUser->id,
        ]);

        Storage::disk('buckets')->makeDirectory($anotherUser->getBucket().'/'.$folder->slug);

        $this
            ->ajax('delete', route('folders.delete', ['folder'=> $folder->id]))
            ->assertForbidden();

        $this->assertTrue(Storage::disk('buckets')->has($anotherUser->getBucket().'/'.$folder->slug));
    }

    /**
     * @test
     */
    public function user_can_download_his_folder()
    {
        $this->signin();

        // Downloading his folder is Allowed
        // --
        $folder = factory(Folder::class)->state('shared')->create([
            'user_id' => Auth::id(),
        ]);

        Storage::disk('buckets')->makeDirectory(Auth::user()->getBucket().'/'.$folder->slug);
        UploadedFile::fake()
            ->create('file.data', 2000)
            ->storeAs($folder->getPath(), 'file.data', 'buckets');

        $this
            ->get(route('folders.download', ['folder'=> $folder->id]))
            ->assertHeader('Content-Disposition', 'attachment; filename='.$folder->slug.'.zip');

        // Downloading another user folder is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->state('shared')->create([
            'user_id' => $anotherUser->id,
        ]);

        $this
            ->get(route('folders.download', ['folder'=> $folder->id]))
            ->assertForbidden();
    }

    // Data providers
    // --

    public function folder_valid_data()
    {
        return [
            [
                [
                    'name' => 'Folder 1',
                ],
            ],
        ];
    }

    public function folder_invalid_data()
    {
        return [
            [
                [
                    'name' => '',
                ],

                [
                    'name',
                ],
            ],
        ];
    }
}
