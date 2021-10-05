<?php

namespace App\Services\SMS;

use App\Services\SMS\Exceptions\ServiceNotSupportedException;
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
     *
     * @param $config
     */
    public static function setupFakeService($config)
    {
        self::$service = new Fake($config);
    }

    /**
     * Provider constructor.
     *
     * @throws ServiceNotSupportedException
     */
    public function __construct()
    {
        if (self::$service != null) {
            return;
        }

        $service = config('services.sms.service');
        if ($service === 'TWILIO') {
            self::$service = new Twilio();
        } elseif ($service === 'FAKE') {
            self::$service = new FAKE();
        } else {
            throw new ServiceNotSupportedException('SMS service not supported "'.$service.'".');
        }
    }

    /**
     * Get the initialized SMS service.
     *
     * @return Twilio|Fake|null
     */
    public function getService()
    {
        return self::$service;
    }
}
