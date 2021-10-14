<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Sunaoka\PushNotifications\Drivers\FCM;
use Sunaoka\PushNotifications\Pusher;

$deviceToken = 'Device token';

$payload = [
    'data.title' => 'Portugal vs. Denmark',
    'data.body'  => 'great match!',
];

$options = new FCM\PlainText\Option();
$options->payload = $payload;
$options->apiKey = 'server-key';

$driver = new FCM\PlainText($options);

$pusher = new Pusher();
$feedback = $pusher->to($deviceToken)->send($driver);

$result = $feedback->isSuccess($deviceToken);
if (! $result) {
    echo $feedback->failure($deviceToken);
}
