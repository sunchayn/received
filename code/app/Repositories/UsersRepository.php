<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UsersRepository
{

    public function create($data)
    {
        return User::create([
            'password' => bcrypt($data['password']),
            'username' => Str::uuid(),
            'phone_number' => $data['phone_number'],
            'country_code' => str_replace('+', '', $data['country_code']),
        ]);
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

    public function clear2FaStatus(User $user)
    {
        $user->ongoing_two_fa = false;
        $user->save();
    }
}
