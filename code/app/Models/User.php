<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

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
 * @property string|null $two_fa_code
 * @property Carbon|null $verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin Builder
 */
class User extends Authenticatable
{
    protected $guarded = [];

    protected $dates = [
        'verified_at',
    ];

    public function isVerified()
    {
        return $this->verified_at != null;
    }

    public function needsTwoFA()
    {
        return $this->two_fa_code != null;
    }
}
