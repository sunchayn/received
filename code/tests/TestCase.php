<?php

namespace Tests;

use App\Models\Folder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use App\Models\User;
use Auth;
use Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Mimic an asynchronous request
     * @param $verb
     * @param $route
     * @param array $data
     * @return TestResponse
     */
    public function ajax($verb, $route, $data = [])
    {
        return $this->json($verb, $route, $data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    }

    /**
     * Create user session with the given $user or authenticate a new one.
     * @param User|null $user
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function signin(User $user = null)
    {
        if (! $user) {
            $user = factory(User::class)->create();
        }

        Auth::login($user);

        return Auth::user();
    }

    /**
     * Make sure that given entity has the expected new data
     *
     * @param $data
     * @param $entity
     */
    protected function assertNewDataIsPersisted($data, $entity)
    {
        foreach ($data as $key => $value) {
            $entityValue = $entity->$key;
            $this->assertEquals($value, $entityValue);
        }
    }

    /**
     * Create a user alongside the needed entities to upload files to a folder.
     *
     * @param $password
     * @param $folder
     * @param $user
     * @param $storageLimit
     */
    protected function prepareFolderForUpload(&$password, &$folder, &$user, $storageLimit)
    {
        $password = '123456';

        $user = factory(User::class)->state('with_subscription')->create();

        # Set plan storage limit
        $user->subscription->plan->update([
            'storage_limit' => $storageLimit,
        ]);

        $folder = factory(Folder::class)->create([
            'user_id' => $user->id,
            'password' => bcrypt($password),
            'shared_at' => now(),
        ]);
    }
}
