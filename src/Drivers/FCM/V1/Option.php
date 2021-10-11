<?php

namespace Sunaoka\PushNotifications\Drivers\FCM\V1;

use Sunaoka\PushNotifications\Drivers\DriverOption;

class Option extends DriverOption
{
    /**
     * @var string|array
     */
    public $credentials = '';

    /**
     * @var string
     */
    public $projectId = '';

    /**
     * @var string[][]
     */
    protected $validationRules = [
        'credentials' => ['required'],
        'projectId'   => ['required'],
    ];
}
