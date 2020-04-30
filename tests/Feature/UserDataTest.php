<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_fetch_his_storage_info()
    {
        $this->signin();

        $this
            ->ajax('get', route('me.storage_info'))
            ->assertOk()
            ->assertJsonStructure([
                'used_storage',
                'total_storage',
                'percentage',
            ]);
    }
}
