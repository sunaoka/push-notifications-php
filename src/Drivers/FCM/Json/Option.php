<?php

namespace Sunaoka\PushNotifications\Drivers\FCM\Json;

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
