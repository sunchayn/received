<?php

namespace App\Services\SMS;

use App\Services\SMS\Providers\Fake;
use App\Services\SMS\Providers\Twilio;

/**
 * @var Twilio|Fake|null $service
 * @mixin
 */
class Provider
{
    private static $service = null;

    /**
     * Create a fake service for testing.
     * @param $config
     */
    public static function setupFakeService($config)
    {
        self::$service = new Fake($config);
    }

    /**
     * Provider constructor.
     */
    public function __construct()
    {
        if (self::$service != null) {
            return;
        }

        self::$service = new Twilio();
    }


    /**
     * Get the initialized SMS service.
     * @return Twilio|Fake|null
     */
    public function getService()
    {
        return self::$service;
    }
}
