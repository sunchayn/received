<?php

namespace App\Services\SMS;

use App\Services\SMS\Exceptions\VerificationCodeNotSentException;
use App\Services\SMS\Exceptions\VerificationNotAchievedException;
use App\Services\SMS\Exceptions\TwoFactorCodeNotSentException;
use App\Services\SMS\Exceptions\UserNotCreatedException;

interface ProviderInterface
{

    /**
     * Send a verification code to the given phone number.
     *
     * @param string $phoneNumber
     * @throws VerificationCodeNotSentException
     * @return string A unique identifier for the request.
     */
    public function sendVerificationCode(string $phoneNumber): string;

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
     * Send a two factor authentication code to the given phone number.
     *
     * @param AuthyContract $user
     * @throws TwoFactorCodeNotSentException
     * @throws UserNotCreatedException
     * @return string A unique identifier for the request.
     */
    public function sendTwoFactorCode(AuthyContract $user): string;

    /**
     * Verify the integrity of the given two factor authentication code.
     *
     * @param string $twoFactorAuthRequestId
     * @param string $code
     * @throws VerificationNotAchievedException
     * @return bool
     */
    public function verifyTwoFactorCode(string $twoFactorAuthRequestId, string $code): bool;
}
