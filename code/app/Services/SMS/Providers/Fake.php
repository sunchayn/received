<?php

namespace App\Services\SMS\Providers;

use App\Services\SMS\AuthyContract;
use App\Services\SMS\ProviderInterface;
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
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function sendVerificationCode(string $phoneNumber): string
    {
        return Str::random(10);
    }

    /**
     * @inheritDoc
     */
    public function verify(string $requestId, string $code): bool
    {
        $shouldSucceed = $this->config['verification_should_succeed'];
        return $shouldSucceed ? $shouldSucceed === true : false;
    }

    /**
     * @inheritDoc
     */
    public function sendTwoFactorCode(AuthyContract $user): string
    {
        return Str::random(10);
    }

    /**
     * @inheritDoc
     */
    public function verifyTwoFactorCode(string $twoFactorAuthRequestId, string $code): bool
    {
        $shouldSucceed = $this->config['two_factor_verification_should_succeed'];
        return $shouldSucceed ? $shouldSucceed === true : false;
    }
}
