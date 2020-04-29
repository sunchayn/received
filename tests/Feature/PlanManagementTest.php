<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Services\SMS\Provider as SMSProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanManagementTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        SMSProvider::setupFakeService([]);
    }

    /**
     * @test
     */
    public function it_create_default_plan_on_user_creation()
    {
        // Create a default plan
        // --
        $plans = factory(Plan::class, 5)->create();
        $plan = $plans->first();
        $plan->update(['is_default' => true]);

        $data = factory(User::class)->make();

        $this
            ->ajax('post', route('auth.signup'), $data->toArray())
            ->assertOk();

        $user = User::first();
        $this->assertEquals($plan->id, $user->subscription->plan->id);
    }
}
