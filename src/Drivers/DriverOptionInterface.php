<?php

namespace Sunaoka\PushNotifications\Drivers;

use Sunaoka\PushNotifications\Exceptions\ValidationException;

interface DriverOptionInterface
{
    /**
     * @param array $options
     */
    public function __construct($options = []);

    /**
     * @return bool
     * @throws ValidationException
     */
    public function validate();
}
