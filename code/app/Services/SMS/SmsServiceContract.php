<?php

namespace App\Services\SMS;

interface SmsServiceContract
{
    /**
     * Get the user Authy app identifier.
     *
     * @return string
     */
    public function getAuthyAppId(): string;

    /**
     * Set an Authy app id for the given user.
     *
     * @param string $id
     * @return mixed
     */
    public function setAuthyAppId(string $id);

    /**
     * Get the user phone number (without the country code).
     *
     * @return string
     */
    public function getPhoneNumber(): string;

    /**
     * Get the user country code.
     *
     * @return string
     */
    public function getCountryCode(): string;
}
