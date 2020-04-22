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
     * @param SmsServiceContract $user
     * @throws VerificationCodeNotSentException
     * @return string A unique identifier for the request.
     */
    public function sendVerificationCode(SmsServiceContract $user): string;

    /**
     * Verify the integrity of the given verification code.
     *
     * @param string $verificationId
     * @param string $code
     * @throws VerificationNotAchievedException
     * @return bool
     */
    public function verify(string $verificationId, string $code): bool;

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
