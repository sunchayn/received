<?php

namespace Tests\Feature;

use App\Events\UserCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use App\Services\SMS\Provider as SMSProvider;

class EventsPropagationTest extends TestCase
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
    public function it_fire_an_event_upon_users_creation()
    {
        Event::fake();

        $data = factory(User::class)->make();

        $this
            ->ajax('post', route('auth.signup'), $data->toArray())
        ;

        Event::assertDispatched(UserCreated::class);
    }
}
