<?php

namespace App\Repositories;

use App\Events\UserCreated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Class UsersRepository.
 */
class UsersRepository
{
    /**
     * Create a user.
     *
     * @param $data
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public function create($data)
    {
        $user = User::create([
            'password' => bcrypt($data['password']),
            'username' => Str::random(8),
            'phone_number' => $data['phone_number'],
            'country_code' => str_replace('+', '', $data['country_code']),
        ]);

        $user->update(['username' => $user->id.'_'.$user->username]);

        // Dispatch event
        event(new UserCreated($user));

        return $user;
    }

    /**
     * Approve user new phone.
     *
     * @param User $user
     * @return User
     */
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

    /**
     * Mark a user as verified.
     *
     * @param User $user
     */
    public function markAsVerified(User $user)
    {
        $user->verification_id = null;
        $user->ongoing_two_fa = false;
        $user->verified_at = Carbon::now();
        $user->save();
    }

    /**
     * Clear ongoing 2FA status from the user.
     *
     * @param User $user
     */
    public function clear2FaStatus(User $user)
    {
        $user->ongoing_two_fa = false;
        $user->save();
    }

    /**
     * Get a user by its verification id.
     *
     * @param $verification_id
     * @return User|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByVerificationId($verification_id)
    {
        return User::where('verification_id', $verification_id)->first();
    }

    /**
     * Get a user by its username.
     * @param $username
     * @return User
     */
    public function getByUsername($username): User
    {
        return User::where('username', $username)->firstOrFail();
    }
}
