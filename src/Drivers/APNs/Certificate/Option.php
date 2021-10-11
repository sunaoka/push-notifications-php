<?php

namespace Sunaoka\PushNotifications\Drivers\APNs\Certificate;

use Sunaoka\PushNotifications\Drivers\DriverOption;

class Option extends DriverOption
{
    /**
     * @var string
     */
    public $certificate = '';

    /**
     * @var string
     */
    public $password = '';

    /**
     * @var string
     */
    public $topic = '';

    /**
     * @var string[][]
     */
    protected $validationRules = [
        'certificate' => ['required'],
        'password'    => ['required'],
        'topic'       => ['required'],
    ];
}
