<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Sunaoka\PushNotifications\Drivers\FCM;
use Sunaoka\PushNotifications\Pusher;

$deviceToken = 'Device token';

$payload = [
    'notification' => [
        'title' => 'Portugal vs. Denmark',
        'body'  => 'great match!',
    ],
];

$options = new FCM\Json\Option();
$options->payload = $payload;
$options->apiKey = 'server-key';

$driver = new FCM\Json($options);

$pusher = new Pusher();
$feedback = $pusher->to($deviceToken)->send($driver);

$result = $feedback->isSuccess($deviceToken);
if (! $result) {
    echo $feedback->failure($deviceToken);
}
