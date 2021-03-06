<?php

namespace App\Services\SMS\Providers;

use App\Services\SMS\Exceptions\UserNotCreatedException;
use App\Services\SMS\Exceptions\VerificationCodeNotSentException;
use App\Services\SMS\Exceptions\VerificationNotAchievedException;
use App\Services\SMS\ProviderInterface;
use App\Services\SMS\SmsServiceContract;
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
            abort(500, 'Invalid SMS configuration.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendVerificationCode(string $phoneNumber): string
    {
        try {
            return $this->client->verify->v2
                ->services(config('services.twilio.verify_service_id'))
                ->verifications
                ->create($phoneNumber, 'sms')
                ->sid;
        } catch (TwilioException $e) {
            \Log::channel('sms')->debug($e->getCode().' '.$e->getMessage());
            throw new VerificationCodeNotSentException('Unable to send verification code.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function verify(SmsServiceContract $user, string $code): bool
    {
        $phoneNumber = '+'.$user->getCountryCode().$user->getPhoneNumber();

        try {
            $verification = $this->client->verify->v2
                ->services(config('services.twilio.verify_service_id'))
                ->verificationChecks
                ->create($code, ['to' => $phoneNumber]);

            return $verification->status === 'approved';
        } catch (TwilioException $e) {
            \Log::channel('sms')->debug($e->getCode().' '.$e->getMessage());
            throw new VerificationNotAchievedException('Unable to verify the code.');
        }
    }

    /**
     * {@inheritdoc}
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
                throw new UserNotCreatedException('Unable to create an authy user.');
            }
        }

        $response = $authy->requestSms($authyId);

        return $response->ok();
    }

    /**
     * {@inheritdoc}
     */
    public function verifyTwoFactorCode(SmsServiceContract $user, string $code): bool
    {
        try {
            $authy = new AuthyApi(config('services.twilio.authy_api_key'));
            $response = $authy->verifyToken($user->getAuthyAppId(), $code);

            return $response->ok();
        } catch (\Exception $e) {
            \Log::channel('sms')->debug($e->getCode().' '.$e->getMessage());

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendSMS(SmsServiceContract $user, string $content): bool
    {
        $phoneNumber = '+'.$user->getCountryCode().$user->getPhoneNumber();

        try {
            $this->client->messages->create(
                $phoneNumber,
                [
                    'from' => config('services.sms.phone_number'),
                    'body' => $content,
                ]
            );

            return true;
        } catch (TwilioException $e) {
            \Log::channel('sms')->debug($e->getCode().' '.$e->getMessage());

            return false;
        }
    }
}
