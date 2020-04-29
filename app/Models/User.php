<?php

namespace App\Models;

use App\Models\Traits\Verification;
use App\Services\SMS\SmsServiceContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\User.
 *
 * @property int $id
 * @property string|null $name
 * @property string $username
 * @property string|null $email
 * @property string $password
 * @property string $phone_number
 * @property string|null $verification_id
 * @property string|null $authy_id
 * @property bool $ongoing_two_fa
 * @property int $country_code
 * @property Carbon|null $verified_at
 * @property Carbon|null $last_code_sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|null $folders
 * @property Collection|null $shared
 * @property OngoingNewPhoneVerification|null $ongoingNewPhoneVerification
 * @property NotificationPrefs $notificationPrefs
 * @property Subscription $subscription
 * @property  Collection|null $notifications
 * @property  Collection|null unreadNotifications
 * @property Plan $plan
 * @mixin Builder
 */
class User extends Authenticatable implements SmsServiceContract
{
    use Verification, Notifiable;

    protected $guarded = [];

    protected $casts = [
        'ongoing_two_fa' => 'boolean',
    ];

    protected $dates = [
        'verified_at',
        'last_code_sent_at',
    ];

    public function folders()
    {
        return $this->hasMany(Folder::class)->latest();
    }

    public function shared()
    {
        return $this
            ->hasMany(Folder::class)
            ->whereNotNull('password')
            ->whereNotNull('shared_at');
    }

    public function ongoingNewPhoneVerification()
    {
        return $this->hasOne(OngoingNewPhoneVerification::class);
    }

    public function notificationPrefs()
    {
        return $this->hasOne(NotificationPrefs::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id')->latest();
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id')->where('is_seen', false)->latest();
    }

    public function unreadNotificationsByType($type)
    {
        return $this->hasMany(Notification::class, 'user_id')->where('is_seen', false)->where('type', $type)->latest();
    }

    public function isVerified()
    {
        return $this->verified_at != null;
    }

    public function needsTwoFa()
    {
        return $this->ongoing_two_fa;
    }

    public function getBucket()
    {
        return 'bucket_'.$this->id;
    }

    /**
     * AuthyContract for double factor authentication
     * --.
     */
    public function getAuthyAppId()
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

    public function setAuthyAppId(string $id)
    {
        $this->authy_id = $id;
        $this->save();
    }
}
