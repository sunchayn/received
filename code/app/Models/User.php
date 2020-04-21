<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use App\Services\SMS\AuthyContract;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $name
 * @property string $username
 * @property string|null $email
 * @property string $password
 * @property string $phone_number
 * @property string|null $verification_id
 * @property string|null $authy_id
 * @property boolean $ongoing_two_fa
 * @property integer $country_code
 * @property Carbon|null $verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin Builder
 */
class User extends Authenticatable implements AuthyContract
{
    protected $guarded = [];

    protected $casts = [
        'ongoing_two_fa' => 'boolean',
    ];

    protected $dates = [
        'verified_at',
    ];

    public function isVerified()
    {
        return $this->verified_at != null;
    }

    public function needsTwoFA()
    {
        return $this->ongoing_two_fa;
    }


    public function getAuthyAppId(): string
    {
        return $this->authy_id;
    }

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    public function getCountryCode(): string
    {
        return $this->country_code;
    }
}
