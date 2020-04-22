<?php


namespace App\Models\Traits;

use App\Services\SMS\Exceptions\TwoFactorCodeNotSentException;
use App\Services\SMS\Exceptions\UserNotCreatedException;
use App\Services\SMS\Exceptions\VerificationCodeNotSentException;
use App\Services\SMS\Exceptions\VerificationNotAchievedException;
use App\Services\SMS\ProviderInterface as SMSProviderInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait Verification
 * Manage communication with SMS service to send and check verification & 2FA codes.
 *
 * @package App\Models\Traits
 */
trait Verification
{
    /**
     * Field mapping for verification request id.
     * @var string
     */
    protected $verificationIdField = 'verification_id';

    /**
     * Field mapping for Ongoing 2FA requests.
     * @var string
     */
    protected $ongoing2FaField = 'ongoing_two_fa';

    /**
     * Field mapping for sms delivery time.
     * @var string
     */
    protected $smsDeliveryTimeField = 'last_code_sent_at';

    /**
     * Determine if the given user can receive a new verification SMS.
     *
     * @return bool
     */
    public function canReceiveCode()
    {
        if (!$this->{$this->smsDeliveryTimeField}) {
            return true;
        }

        return $this->{$this->smsDeliveryTimeField}->diffInMinutes(Carbon::now()) > 1;
    }

    /**
     * Send a verification code to user phone.
     *
     * @return bool|string
     */
    public function sendVerificationCode()
    {
        try {
            /**
             * @var SMSProviderInterface $smsProvider
             */
            $smsProvider = app()->make('SMS');

            $verificationRequestId = $smsProvider->sendVerificationCode($this);
            $this->{$this->verificationIdField} = $verificationRequestId;
            $this->{$this->smsDeliveryTimeField} = Carbon::now();
            $this->save();

            return $verificationRequestId;
        } catch (BindingResolutionException $e) {
            abort('Unable to resolve SMS service', 500);
            return false;
        } catch (VerificationCodeNotSentException $e) {
            return false;
        }
    }

    /**
     * Check the integrity of the given code.
     *
     * @param String $code
     * @return bool
     */
    public function checkVerificationCode(String $code)
    {
        try {
            /**
             * @var SMSProviderInterface $smsProvider
             */
            $smsProvider = app()->make('SMS');

            return $smsProvider->verify($this->{$this->verificationIdField}, $code);
        } catch (VerificationNotAchievedException $e) {
            return false;
        } catch (BindingResolutionException $e) {
            abort('Unable to resolve SMS service', 500);
            return false;
        }
    }

    /**
     * Send a 2FA code to the given user.
     * @return bool
     */
    public function send2FaCode()
    {
        try {
            /**
             * @var SMSProviderInterface $smsProvider
             */
            $smsProvider = app()->make('SMS');

            $delivered = $smsProvider->sendTwoFactorCode($this);

            if ($delivered) {
                $this->{$this->smsDeliveryTimeField} = Carbon::now();
            }

            $this->{$this->ongoing2FaField}  = true;
            $this->save();

            return $delivered;
        } catch (UserNotCreatedException $e) {
            return false;
        } catch (BindingResolutionException $e) {
            abort('Unable to resolve SMS service', 500);
            return false;
        }
    }

    /**
     * Check the integrity of the given 2FA code.
     *
     * @param String $code
     * @return bool
     */
    public function check2FaCode(String $code)
    {
        try {
            /**
             * @var SMSProviderInterface $smsProvider
             */
            $smsProvider = app()->make('SMS');
            return $smsProvider->verifyTwoFactorCode($this, $code);
        } catch (BindingResolutionException $e) {
            abort('Unable to resolve SMS service', 500);
            return false;
        }
    }
}
