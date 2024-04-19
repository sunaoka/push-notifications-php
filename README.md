# Push notifications Library for PHP

[![Latest](https://poser.pugx.org/sunaoka/push-notifications-php/v)](https://packagist.org/packages/sunaoka/push-notifications-php)
[![License](https://poser.pugx.org/sunaoka/push-notifications-php/license)](https://packagist.org/packages/sunaoka/push-notifications-php)
[![PHP](https://img.shields.io/packagist/php-v/sunaoka/push-notifications-php)](composer.json)
[![Test](https://github.com/sunaoka/push-notifications-php/actions/workflows/test.yml/badge.svg)](https://github.com/sunaoka/push-notifications-php/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/sunaoka/push-notifications-php/branch/develop/graph/badge.svg)](https://codecov.io/gh/sunaoka/push-notifications-php)

---

## Supported Protocols

| Protocol                   | Supported  | Driver             | Options                   |
| -------------------------- | :--------: | ------------------ | ------------------------- |
| APNs ([Token Based])       |  &check;   | `APNs\Token`       | `APNs\Token\Option`       |
| APNs ([Certificate Based]) |  &check;   | `APNs\Certificate` | `APNs\Certificate\Option` |
| APNs ([Binary Provider])   |            |                    |                           |
| FCM ([HTTP v1])            |  &check;   | `FCM\V1`           | `FCM\V1\Option`           |
| FCM ([Legacy JSON])        | Deprecated | `FCM\Json`         | `FCM\Json\Option`         |
| FCM ([Legacy Plain Text])  | Deprecated | `FCM\PlainText`    | `FCM\PlainText\Option`    |
| FCM ([XMPP])               |            |                    |                           |

## Installation

```bash
composer require sunaoka/push-notifications-php
```

## Basic Usage

For example, in the case of APNs [Token Based].

```php
<?php

use Sunaoka\PushNotifications\Drivers\APNs;
use Sunaoka\PushNotifications\Pusher;

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
$feedback = $pusher->to('Device token')->send($driver);

$result = $feedback->isSuccess('Device token');
if (! $result) {
    echo $feedback->failure('Device token');
    // BadDeviceToken
}
```

## How to specify options

There are two ways to specify the option.

```php
$options = new APNs\Token\Option();
$options->payload = $payload;
$options->authKey = '/path/to/key.p8';
$options->keyId = 'ABCDE12345';
$options->teamId = 'ABCDE12345';
$options->topic = 'com.example.app';
```

or

```php
$options = new APNs\Token\Option([
    'payload' => $payload,
    'authKey' => '/path/to/key.p8',
    'keyId'   => 'ABCDE12345',
    'teamId'  => 'ABCDE12345',
    'topic'   => 'com.example.app',
]);
```

## Multicast message

Specify an array of device tokens in `Pusher::to()`.
Then, you can distribute to multiple devices.

```php
$pusher = new Pusher();
$feedback = $pusher->to([
    'Device token 1',
    'Device token 2',
    'Device token 3',
])->send($driver);
```

## How to switch between the production and development environments (only APNs)

This is specified as an argument when creating an instance of `Pusher`.

```php
// Development environment (default)
$pusher = new Pusher(false);
```

```php
// Production environment
$pusher = new Pusher(true);
```

## Feedback

The return value of `Pusher::send()` is a `Feedback` object.

With the `Feedback` object, you can determine whether the notification succeeded or failed.

```php
$pusher = new Pusher();
$feedback = $pusher->to('Device token')->send($driver);

$result = $feedback->isSuccess('Device token');
if ($result) {
    echo $feedback->success('Device token');
    // 01234567-0123-0123-0123-01234567890A
} else {
    echo $feedback->failure('Device token');
    // BadDeviceToken
}
```

## HTTP Request Option

You can specify [Guzzle Request Options] as a driver option.

```php
$options = new APNs\Token\Option();
$options->httpOptions = [
    'connect_timeout' => 3.14
    'timeout'         => 3.14,
    'debug'           => true,
];
```

## More examples

More examples can be found in the [examples](example) directory.


[Token Based]: https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/establishing_a_token-based_connection_to_apns
[Certificate Based]: https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/establishing_a_certificate-based_connection_to_apns
[Binary Provider]: https://developer.apple.com/library/archive/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/BinaryProviderAPI.html
[HTTP v1]: https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages/send
[Legacy JSON]: https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
[Legacy Plain Text]: https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-plain-text
[XMPP]: https://firebase.google.com/docs/cloud-messaging/xmpp-server-ref
[Guzzle Request Options]: https://docs.guzzlephp.org/en/stable/request-options.html
