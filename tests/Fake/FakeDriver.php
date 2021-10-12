<?php

namespace Sunaoka\PushNotifications\Tests\Fake;

use Sunaoka\PushNotifications\Drivers\Driver;

class FakeDriver extends Driver
{
    public function __construct($options)
    {
    }

    public function send()
    {
    }
}
