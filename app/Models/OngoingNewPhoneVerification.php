<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\OngoingNewPhoneVerification.
 *
 * @property int $id
 * @property string $phone_number
 * @property string $verification_id
 * @property int $country_code
 * @property Carbon|null $verified_at
 * @property Carbon|null $last_code_sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection|null $folders
 *
 * @mixin Builder
 */
class OngoingNewPhoneVerification extends Model
{
    protected $guarded = [];
}
