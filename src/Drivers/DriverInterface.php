<?php

namespace Sunaoka\PushNotifications\Drivers;

interface DriverInterface
{
    /**
     * @param DriverOptionInterface $options
     */
    public function __construct($options);

    /**
     * @return DriverOptionInterface
     */
    public function getOptions();

    /**
     * @param string[] $devices
     *
     * @return self
     */
    public function to($devices);

    /**
     * @return Feedback
     */
    public function send();

    /**
     * @param bool $production
     *
     * @return self
     */
    public function setProduction($production);

    /**
     * @return bool
     */
    public function isProduction();
}
