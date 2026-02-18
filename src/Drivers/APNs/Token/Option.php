<?php

namespace Sunaoka\PushNotifications\Drivers\APNs\Token;

use Sunaoka\PushNotifications\Drivers\DriverOption;

class Option extends DriverOption
{
    /**
     * @var string
     */
    public $authKey;

    /**
     * @var string
     */
    public $keyId;

    /**
     * @var string
     */
    public $teamId;

    /**
     * @var string
     */
    public $topic;

    /**
     * @var string[]
     */
    protected $required = [
        'authKey',
        'keyId',
        'teamId',
        'topic',
    ];
}
