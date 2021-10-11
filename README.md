# Push notifications Library for PHP

[![License](https://poser.pugx.org/sunaoka/push-notifications-php/license)](https://packagist.org/packages/sunaoka/push-notifications-php)
[![PHP](https://img.shields.io/packagist/php-v/sunaoka/push-notifications-php)](composer.json)
[![Test](https://github.com/sunaoka/push-notifications-php/actions/workflows/test.yml/badge.svg)](https://github.com/sunaoka/push-notifications-php/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/sunaoka/push-notifications-php/branch/develop/graph/badge.svg)](https://codecov.io/gh/sunaoka/push-notifications-php)

---

## Supported Protocols

| Protocol                   | Supported |
| -------------------------- | :-------: |
| APNs ([Token Based])       |  &check;  |
| APNs ([Certificate Based]) |  &check;  |
| APNs ([Binary Provider])   |           |
| FCM ([HTTP v1])            |  &check;  |
| FCM ([Legacy JSON])        |  &check;  |
| FCM ([Legacy Plain Text])  |  &check;  |
| FCM ([XMPP])               |           |

## Installation

```bash
composer require sunaoka/push-notifications-php
```

[Token Based]: https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/establishing_a_token-based_connection_to_apns
[Certificate Based]: https://developer.apple.com/documentation/usernotifications/setting_up_a_remote_notification_server/establishing_a_certificate-based_connection_to_apns
[Binary Provider]: https://developer.apple.com/library/archive/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/BinaryProviderAPI.html
[HTTP v1]: https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages/send
[Legacy JSON]: https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-json
[Legacy Plain Text]: https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream-http-messages-plain-text
[XMPP]: https://firebase.google.com/docs/cloud-messaging/xmpp-server-ref
