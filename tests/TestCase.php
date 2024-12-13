<?php

namespace Sunaoka\PushNotifications\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use Assert;

    /**
     * @param string $dir
     *
     * @return string
     */
    protected function certs($dir)
    {
        return __DIR__ . '/Fake/Certs' . $dir;
    }
}
