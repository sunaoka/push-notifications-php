<?php

namespace Sunaoka\PushNotifications;

use Sunaoka\PushNotifications\Drivers\DriverInterface;
use Sunaoka\PushNotifications\Drivers\Feedback;
use Sunaoka\PushNotifications\Exceptions\ValidationException;

class Pusher
{
    /**
     * @var bool
     */
    protected $production;

    /**
     * @var array
     */
    private $devices = [];

    /**
     * @param bool $production
     */
    public function __construct($production = false)
    {
        $this->production = $production;
    }

    /**
     * @param array|string $devices
     *
     * @return self
     */
    public function to($devices)
    {
        $this->devices = is_array($devices) ? $devices : [$devices];

        return $this;
    }

    /**
     * @param DriverInterface $driver
     *
     * @return Feedback
     *
     * @throws ValidationException
     */
    public function send($driver)
    {
        if (count($this->devices) === 0) {
            throw new ValidationException('Device is required');
        }

        $driver->getOptions()->validate();

        return $driver->setProduction($this->production)->to($this->devices)->send();
    }
}
