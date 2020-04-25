<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use App\Models\User;
use Auth;

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
}
