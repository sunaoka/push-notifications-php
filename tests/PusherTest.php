<?php

namespace Sunaoka\PushNotifications\Tests;

use Sunaoka\PushNotifications\Exceptions\ValidationException;
use Sunaoka\PushNotifications\Pusher;
use Sunaoka\PushNotifications\Tests\Fake\FakeDriver;
use Sunaoka\PushNotifications\Tests\Fake\FakeOption;

class PusherTest extends TestCase
{
    /**
     * @return void
     *
     * @throws ValidationException
     */
    public function testSendFailure()
    {
        $this->expectExceptionCompat(ValidationException::class);

        $pusher = new Pusher();
        $pusher->to([])->send(new FakeDriver(new FakeOption()));
    }
}
