<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Sunaoka\PushNotifications\Drivers\FCM;
use Sunaoka\PushNotifications\Pusher;

$deviceToken = 'Device token';

$payload = [
    'message' => [
        'notification' => [
            'title' => 'Portugal vs. Denmark',
            'body'  => 'great match!',
        ],
    ],
];

$options = new FCM\V1\Option();
$options->payload = $payload;
$options->credentials = 'serviceAccountKey.json';
$options->projectId = 'project-id-abc12';

$driver = new FCM\V1($options);

$pusher = new Pusher();
$feedback = $pusher->to($deviceToken)->send($driver);

$result = $feedback->isSuccess($deviceToken);
if (! $result) {
    echo $feedback->failure($deviceToken);
}
