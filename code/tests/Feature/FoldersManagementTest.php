<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Auth;
use Storage;

class FoldersManagementTest extends TestCase
{
    use RefreshDatabase;

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
            ->assertJsonCount($foldersCount)
        ;
    }

    /**
     * @dataProvider folder_valid_data
     * @param $data
     * @test
     */
    public function user_can_create_a_folder($data)
    {
        $this->signin();

        $this
            ->post(route('folders.create'), $data)
            ->assertStatus(201)
        ;

        $this->assertTrue(Auth::user()->folders->isNotEmpty());

        // Folder is created
        // --
        $bucket = Auth::user()->getBucket();
        $folder = Auth::user()->folders->first();
        $this->assertTrue(Storage::disk('buckets')->has($bucket . '/' . $folder->slug));
    }


    /**
     * @dataProvider folder_valid_data
     * @param $data
     * @test
     */
    public function it_create_user_bucket_when_it_does_not_exists($data) {
        $this->signin();

        // Clean bucket directory
        Storage::disk('buckets')->deleteDirectory(Auth::user()->getBucket());

        $this->assertFalse(Storage::disk('buckets')->has(Auth::user()->getBucket()));

        $this
            ->post(route('folders.create'), $data)
        ;

        $this->assertTrue(Storage::disk('buckets')->has(Auth::user()->getBucket()));
    }

    /**
     * @dataProvider folder_valid_data
     * @param $data
     * @test
     */
    public function it_create_folder_in_storage($data) {
        $this->signin();

        $this
            ->post(route('folders.create'), $data)
        ;

        $bucket = Auth::user()->getBucket();
        $folder = Auth::user()->folders->first();

        $this->assertTrue(Storage::disk('buckets')->has($bucket . '/' . $folder->slug));
    }

    /**
     * @dataProvider folder_valid_data
     * @param $data
     * @test
     */
    public function it_does_not_allow_unverified_users_to_create_folder($data) {
        $user = factory(User::class)->state('not_verified')->create();
        $this->signin( $user );

        $this
            ->post(route('folders.create'), $data)
            ->assertStatus(302)
        ;

        $this->assertTrue(Auth::user()->folders->isEmpty());
    }

    /**
     * @dataProvider folder_invalid_data
     * @param $data
     * @param $expectedErrors
     * @test
     */
    public function it_does_not_allow_invalid_data_when_creating_folders($data, $expectedErrors) {
        $this->signin();

        $this
            ->json('post', route('folders.create'), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors($expectedErrors)
        ;
    }

    /**
     * @dataProvider folder_valid_data
     * @param $data
     * @test
     */
    public function it_does_not_allow_multiple_folders_with_the_same_name($data) {
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
            ])
        ;

        // Folder with the same name for another user
        // --

        # Delete the previously deleted folder
        Auth::user()->folders()->first()->delete();

        factory(Folder::class)->create([
            'user_id' => factory(User::class)->create()->id,
            'name' => $data['name'],
        ]);

        $this
            ->post(route('folders.create'), $data)
            ->assertCreated()
        ;
    }


    /**
     * @test
     */
    public function user_can_share_a_folder() {
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
            ->assertOk()
        ;

        $this->assertTrue($folder->refresh()->isShared());

        // Sharing another user folder is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->create([
            'user_id' => $anotherUser->id,
        ]);

        $this
            ->ajax('patch', route('folders.share', ['folder' => $folder->id]), $data)
            ->assertUnauthorized()
        ;
    }

    /**
     * @dataProvider folder_valid_data
     * @param $data
     * @test
     */
    public function user_can_update_his_folder($data) {
        $this->signin();

        // Updating his folder is Allowed
        // --
        $folder = factory(Folder::class)->create([
            'user_id' => Auth::id(),
        ]);

        $this
            ->ajax('patch', route('folders.edit', ['folder' => $folder->id]), $data)
            ->assertOk()
        ;

        $this->assertNewDataIsPersisted($data, $folder->refresh());

        // Sharing another user folder is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->create([
            'user_id' => $anotherUser->id,
        ]);

        $this
            ->ajax('patch', route('folders.edit', ['folder' => $folder->id]), $data)
            ->assertUnauthorized()
        ;
    }

    /**
     * @test
     */
    public function user_can_revoke_sharing_access() {
        $this->signin();

        // Revoking his folder is Allowed
        // --
        $folder = factory(Folder::class)->state('shared')->create([
            'user_id' => Auth::id(),
        ]);

        $this
            ->ajax('patch', route('folders.revoke', ['folder'=> $folder->id]))
            ->assertOk()
        ;

        $this->assertFalse($folder->refresh()->isShared());

        // Revoking another user's folder is Forbidden
        // --
        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->state('shared')->create([
            'user_id' => $anotherUser->id,
        ]);

        $this
            ->ajax('patch', route('folders.revoke', ['folder'=> $folder->id]))
            ->assertUnauthorized()
        ;

        $this->assertTrue($folder->refresh()->isShared());
    }

    /**
     * @test
     */
    public function user_can_delete_his_folder() {
        $this->signin();

        // Deleting his folder is Allowed
        // --
        $folder = factory(Folder::class)->state('shared')->create([
            'user_id' => Auth::id(),
        ]);

        Storage::disk('buckets')->makeDirectory(Auth::user()->getBucket() . '/' . $folder->slug);

        $this
            ->ajax('delete', route('folders.delete', ['folder'=> $folder->id]))
            ->assertNoContent()
        ;

        ## Folder is deleted from storage
        ## --
        $this->assertFalse(Storage::disk('buckets')->has(Auth::user()->getBucket() . '/' . $folder->slug));

        // Deleting another user's folder is Forbidden
        // --

        $anotherUser = factory(User::class)->create();
        $folder = factory(Folder::class)->state('shared')->create([
            'user_id' => $anotherUser->id,
        ]);

        Storage::disk('buckets')->makeDirectory($anotherUser->getBucket() . '/' . $folder->slug);

        $this
            ->ajax('delete', route('folders.delete', ['folder'=> $folder->id]))
            ->assertUnauthorized()
        ;

        $this->assertTrue(Storage::disk('buckets')->has($anotherUser->getBucket() . '/' . $folder->slug));
    }

    // Data providers
    // --

    public function folder_valid_data()
    {
        return [
            [
                [
                    'name' => 'Folder 1',
                ]
            ]
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
                ]
            ]
        ];
    }
}
