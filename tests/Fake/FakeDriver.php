<?php

namespace Sunaoka\PushNotifications\Tests\Fake;

use Sunaoka\PushNotifications\Drivers\Driver;
use Sunaoka\PushNotifications\Drivers\DriverOptionInterface;
use Sunaoka\PushNotifications\Drivers\Feedback;

class FakeDriver extends Driver
{
    /**
     * @param DriverOptionInterface $options
     *
     * @phpstan-ignore constructor.unusedParameter
     */
    public function __construct($options) {}

    /**
     * @return Feedback
     *
     * @phpstan-ignore return.missing
     */
    public function send() {}
}
