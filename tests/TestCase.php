<?php

namespace Sunaoka\PushNotifications\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use Assert;

    protected function certs($dir)
    {
        return __DIR__ . '/Fake/Certs' . $dir;
    }
}
