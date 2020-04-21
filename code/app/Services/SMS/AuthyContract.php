<?php

namespace App\Services\SMS;

interface AuthyContract
{

    public function getAuthyAppId(): string;

    public function getPhoneNumber(): string;

    public function getCountryCode(): string;
}
