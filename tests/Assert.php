<?php

namespace Sunaoka\PushNotifications\Tests;

/**
 * @mixin \PHPUnit\Framework\TestCase
 */
trait Assert
{
    /**
     * @param string $exception
     */
    public function expectExceptionCompat($exception)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException($exception);
            return;
        }

        if (method_exists($this, 'setExpectedException')) {
            $this->setExpectedException($exception);
            return;
        }

        $this->fail('Assertion not found');
    }
}
