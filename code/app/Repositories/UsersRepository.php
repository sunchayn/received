<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Events\UserCreated;

class UsersRepository
{

    public function create($data)
    {
        $user = User::create([
            'password' => bcrypt($data['password']),
            'username' => Str::uuid(),
            'phone_number' => $data['phone_number'],
            'country_code' => str_replace('+', '', $data['country_code']),
        ]);

        // Dispatch event
        event(new UserCreated($user));

        return $user;
    }

    public function getByVerificationId($verification_id)
    {
        return User::where('verification_id', $verification_id)->first();
    }

    public function markAsVerified(User $user)
    {
        $user->verification_id = null;
        $user->ongoing_two_fa = false;
        $user->verified_at = Carbon::now();
        $user->save();
    }

    public function confirmOngoingPhoneVerification(User $user)
    {
        $user->update([
            'verification_id' => null,
            'ongoing_two_fa' => false,
            'verified_at' => Carbon::now(),
            'phone_number' => $user->ongoingNewPhoneVerification->phone_number,
            'country_code' => $user->ongoingNewPhoneVerification->country_code,
        ]);

        $user->ongoingNewPhoneVerification->delete();

        return $user;
    }

    public function clear2FaStatus(User $user)
    {
        $user->ongoing_two_fa = false;
        $user->save();
    }

    public function getByUsername($username): User
    {
        return User::where('username', $username)->firstOrFail();
    }
}
