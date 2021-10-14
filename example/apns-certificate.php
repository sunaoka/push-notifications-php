<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Sunaoka\PushNotifications\Drivers\APNs;
use Sunaoka\PushNotifications\Pusher;

$deviceToken = 'Device token';

$payload = [
    'aps' => [
        'alert' => [
            'title' => 'Game Request',
            'body'  => 'Bob wants to play poker',
        ],
    ],
];

$options = new APNs\Certificate\Option();
$options->payload = $payload;
$options->certificate = '/path/to/cert.pem';
$options->password = 'password of certificate';
$options->topic = 'com.example.app';

$driver = new APNs\Certificate($options);

$pusher = new Pusher();
$feedback = $pusher->to($deviceToken)->send($driver);

$result = $feedback->isSuccess($deviceToken);
if (! $result) {
    echo $feedback->failure($deviceToken);
}
