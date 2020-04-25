<?php

namespace App\Services\SMS;

use App\Services\SMS\Exceptions\VerificationCodeNotSentException;
use App\Services\SMS\Exceptions\VerificationNotAchievedException;
use App\Services\SMS\Exceptions\UserNotCreatedException;

interface ProviderInterface
{
    /**
     * Send a verification code to the given user
     *
     * @param string $phoneNumber
     * @throws VerificationCodeNotSentException
     * @return string A unique identifier for the request.
     */
    public function sendVerificationCode(string $phoneNumber): string;

    /**
     * Verify the integrity of the given verification code.
     *
     * @param SmsServiceContract $user
     * @param string $code
     * @throws VerificationNotAchievedException
     * @return bool
     */
    public function verify(SmsServiceContract $user, string $code): bool;

    /**
     * Send a 2FA code to the given user
     *
     * @param SmsServiceContract $user
     * @throws UserNotCreatedException
     * @return bool Indicates whether the code has been sent or not.
     */
    public function sendTwoFactorCode(SmsServiceContract $user): bool;

    /**
     * Verify the integrity of the given 2FA code.
     *
     * @param SmsServiceContract $user
     * @param string $code
     * @return bool
     */
    public function verifyTwoFactorCode(SmsServiceContract $user, string $code): bool;
}
