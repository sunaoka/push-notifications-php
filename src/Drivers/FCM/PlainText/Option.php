<?php

namespace Sunaoka\PushNotifications\Drivers\FCM\PlainText;

use Sunaoka\PushNotifications\Drivers\DriverOption;

/**
 * @deprecated HTTP legacy APIs was deprecated on June 20, 2023, and will be removed in June 2024.
 */
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
