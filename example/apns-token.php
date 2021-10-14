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

$options = new APNs\Token\Option();
$options->payload = $payload;
$options->authKey = '/path/to/key.p8';
$options->keyId = 'ABCDE12345';
$options->teamId = 'ABCDE12345';
$options->topic = 'com.example.app';

$driver = new APNs\Token($options);

$pusher = new Pusher();
$feedback = $pusher->to($deviceToken)->send($driver);

$result = $feedback->isSuccess($deviceToken);
if (! $result) {
    echo $feedback->failure($deviceToken);
}
