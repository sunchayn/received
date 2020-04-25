<?php

namespace App\Services\SMS\Providers;

use App\Services\SMS\SmsServiceContract;
use App\Services\SMS\ProviderInterface;
use App\Services\SMS\Exceptions\UserNotCreatedException;
use App\Services\SMS\Exceptions\TwoFactorCodeNotSentException;
use App\Services\SMS\Exceptions\VerificationCodeNotSentException;
use App\Services\SMS\Exceptions\VerificationNotAchievedException;
use Authy\AuthyApi;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class Twilio implements ProviderInterface
{
    /**
     * @var Client|null
     */
    protected $client = null;

    /**
     * Twilio constructor.
     */
    public function __construct()
    {
        try {
            $this->client = new Client(config('services.sms.key'), config('services.sms.secret'));
        } catch (ConfigurationException $e) {
            abort(500, "Invalid SMS configuration.");
        }
    }

    /**
     * @inheritDoc
     */
    public function sendVerificationCode(SmsServiceContract $user): string
    {
        $phoneNumber = '+' . $user->getCountryCode() . $user->getPhoneNumber();

        try {
            return $this->client->verify->v2
                ->services(config('services.twilio.verify_service_id'))
                ->verifications
                ->create($phoneNumber, "sms")
                ->sid
            ;
        } catch (TwilioException $e) {
            \Log::channel('sms')->debug($e->getCode() . ' ' . $e->getMessage());
            throw new VerificationCodeNotSentException("Unable to send verification code.");
        }
    }

    /**
     * @inheritDoc
     */
    public function verify(SmsServiceContract $user, string $code): bool
    {
        $phoneNumber = '+' . $user->getCountryCode() . $user->getPhoneNumber();
        $verification = null;
        try {
            $verification = $this->client->verify->v2
                ->services(config('services.twilio.verify_service_id'))
                ->verificationChecks
                ->create($code, ['to' => $phoneNumber])
            ;

            return $verification->status === 'approved';
        } catch (TwilioException $e) {
            \Log::channel('sms')->debug($e->getCode() . ' ' . $e->getMessage());
            throw new VerificationNotAchievedException("Unable to verify the code.");
        }
    }

    /**
     * @inheritDoc
     */
    public function sendTwoFactorCode(SmsServiceContract $user): bool
    {
        $authy = new AuthyApi(config('services.twilio.authy_api_key'));

        $authyId = $user->getAuthyAppId();


        if (is_null($authyId)) {
            $authyUser = $authy->registerUser(
                config('services.twilio.authy_email'),
                $user->getPhoneNumber(),
                $user->getCountryCode(),
                false
            );

            if ($authyUser->ok()) {
                $authyId = $authyUser->id();
                $user->setAuthyAppId($authyId);
            } else {
                throw new UserNotCreatedException("Unable to create an authy user.");
            }
        }

        $response = $authy->requestSms($authyId);

        return $response->ok();
    }

    /**
     * @inheritDoc
     */
    public function verifyTwoFactorCode(SmsServiceContract $user, string $code): bool
    {
        try {
            $authy = new AuthyApi(config('services.twilio.authy_api_key'));
            $response = $authy->verifyToken($user->getAuthyAppId(), $code);

            return $response->ok();
        } catch (\Exception $e) {
            \Log::channel('sms')->debug($e->getCode() . ' ' . $e->getMessage());
            return false;
        }
    }
}
