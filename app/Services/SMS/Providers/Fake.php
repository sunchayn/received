<?php

namespace App\Services\SMS\Providers;

use App\Services\SMS\ProviderInterface;
use App\Services\SMS\SmsServiceContract;
use Illuminate\Support\Str;

class Fake implements ProviderInterface
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * Fake constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function sendVerificationCode(string $phoneNumber): string
    {
        return Str::random(10);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(SmsServiceContract $user, string $code): bool
    {
        $shouldSucceed = $this->config['verification_should_succeed'] ?? true;

        return $shouldSucceed ? $shouldSucceed === true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function sendTwoFactorCode(SmsServiceContract $user): bool
    {
        return Str::random(10);
    }

    /**
     * {@inheritdoc}
     */
    public function verifyTwoFactorCode(SmsServiceContract $user, string $code): bool
    {
        $shouldSucceed = $this->config['two_factor_verification_should_succeed'] ?? true;

        return $shouldSucceed ? $shouldSucceed === true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function sendSMS(SmsServiceContract $user, string $content): bool
    {
        $shouldSucceed = $this->config['sms_sending_should_succeed'] ?? true;

        return $shouldSucceed ? $shouldSucceed === true : false;
    }
}
