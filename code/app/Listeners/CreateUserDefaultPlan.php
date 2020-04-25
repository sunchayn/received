<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateUserDefaultPlan
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $plan = Plan::default();

        if (! $plan) {
            return;
        }

        $event->user->subscription()->create([
            'plan_id' => $plan->id,
        ]);
    }
}
