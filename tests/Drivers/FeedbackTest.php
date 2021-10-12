<?php

namespace Sunaoka\PushNotifications\Tests\Drivers;

use Sunaoka\PushNotifications\Drivers\Feedback;
use Sunaoka\PushNotifications\Tests\TestCase;

class FeedbackTest extends TestCase
{
    public function test()
    {
        $feedback = new Feedback();

        $feedback->addSuccess('token1', 'success');
        self::assertTrue($feedback->isSuccess('token1'));
        self::assertSame('success', $feedback->success('token1'));

        $feedback->addFailure('token2', 'failure');
        self::assertFalse($feedback->isSuccess('failure'));
        self::assertSame('failure', $feedback->failure('token2'));

        $feedback->clear();
        self::assertNull($feedback->success('token1'));
        self::assertNull($feedback->success('token2'));
    }
}
