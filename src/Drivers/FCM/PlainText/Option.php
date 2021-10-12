<?php

namespace Sunaoka\PushNotifications\Drivers\FCM\PlainText;

use Sunaoka\PushNotifications\Drivers\DriverOption;

class Option extends DriverOption
{
    /**
     * @var string
     */
    public $apiKey = '';

    /**
     * @var string[][]
     */
    protected $validationRules = [
        'apiKey' => ['required'],
    ];
}
