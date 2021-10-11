<?php

namespace Sunaoka\PushNotifications\Tests\Drivers\FCM;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Sunaoka\PushNotifications\Drivers\FCM;
use Sunaoka\PushNotifications\Exceptions\OptionTypeError;
use Sunaoka\PushNotifications\Exceptions\ValidationException;
use Sunaoka\PushNotifications\Pusher;
use Sunaoka\PushNotifications\Tests\Fake\FakeOption;
use Sunaoka\PushNotifications\Tests\TestCase;

class V1Test extends TestCase
{
    public function testSingleToken()
    {
        $payload = [
            'message' => [
                'notification' => [
                    'title' => 'title',
                ],
            ],
        ];

        $options = new FCM\V1\Option();
        $options->payload = $payload;
        $options->credentials = $this->certs('/fake.json');
        $options->projectId = 'fake-project-id';

        $driver = new FCM\V1($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], json_encode([
                    'name' => 'projects/fake-project-id/messages/0:1632441600000000%d00000000000000a',
                ])),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to('1234567890')
            ->send($driver);

        self::assertSame('projects/fake-project-id/messages/0:1632441600000000%d00000000000000a', $feedback->success('1234567890'));
    }

    public function testMultipleToken()
    {
        $payload = [
            'message' => [
                'notification' => [
                    'title' => 'title',
                ],
            ],
        ];

        $options = new FCM\V1\Option();
        $options->payload = $payload;
        $options->credentials = $this->certs('/fake.json');
        $options->projectId = 'fake-project-id';

        $driver = new FCM\V1($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], json_encode([
                    'name' => 'projects/fake-project-id/messages/0:1632441600000000%d00000000000000a',
                ])),
                new Response(200, [], json_encode([
                    'name' => 'projects/fake-project-id/messages/0:1632441600000000%d00000000000000b',
                ])),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to([
            '1234567890',
            'abcdefghij',
        ])
            ->send($driver);

        self::assertSame('projects/fake-project-id/messages/0:1632441600000000%d00000000000000a', $feedback->success('1234567890'));
        self::assertSame('projects/fake-project-id/messages/0:1632441600000000%d00000000000000b', $feedback->success('abcdefghij'));
    }

    public function testSingleFailure()
    {
        $payload = [
            'message' => [
                'notification' => [
                    'title' => 'title',
                ],
            ],
        ];

        $options = new FCM\V1\Option();
        $options->payload = $payload;
        $options->credentials = $this->certs('/fake.json');
        $options->projectId = 'fake-project-id';

        $driver = new FCM\V1($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(400, [], json_encode([
                    'error' => [
                        'code'    => 400,
                        'message' => 'The registration token is not a valid FCM registration token',
                        'status'  => 'INVALID_ARGUMENT',
                        'details' => [
                            [
                                '@type'     => 'type.googleapis.com/google.firebase.fcm.v1.FcmError',
                                'errorCode' => 'INVALID_ARGUMENT',
                            ],
                        ],
                    ],
                ])),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to('1234567890')
            ->send($driver);

        self::assertSame('INVALID_ARGUMENT', $feedback->failure('1234567890'));
    }

    public function testMultipleFailure()
    {
        $payload = [
            'message' => [
                'notification' => [
                    'title' => 'title',
                ],
            ],
        ];

        $options = new FCM\V1\Option();
        $options->payload = $payload;
        $options->credentials = $this->certs('/fake.json');
        $options->projectId = 'fake-project-id';

        $driver = new FCM\V1($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], json_encode([
                    'name' => 'projects/fake-project-id/messages/0:1632441600000000%d00000000000000a',
                ])),
                new Response(400, [], json_encode([
                    'error' => [
                        'code'    => 400,
                        'message' => 'The registration token is not a valid FCM registration token',
                        'status'  => 'INVALID_ARGUMENT',
                        'details' => [
                            [
                                '@type'     => 'type.googleapis.com/google.firebase.fcm.v1.FcmError',
                                'errorCode' => 'INVALID_ARGUMENT',
                            ],
                        ],
                    ],
                ])),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to([
            '1234567890',
            'abcdefghij',
        ])
            ->send($driver);

        self::assertSame('projects/fake-project-id/messages/0:1632441600000000%d00000000000000a', $feedback->success('1234567890'));
        self::assertSame('INVALID_ARGUMENT', $feedback->failure('abcdefghij'));
    }

    public function testMakeOption()
    {
        $payload = [
            'data.key' => 'value',
        ];

        $options = new FCM\V1\Option([
            'payload'     => $payload,
            'credentials' => $this->certs('/fake.json'),
            'projectId'   => 'fake-project-id',
        ]);

        self::assertSame($payload, $options->payload);
        self::assertSame($this->certs('/fake.json'), $options->credentials);
        self::assertSame('fake-project-id', $options->projectId);
    }

    public function testValidateOption()
    {
        $this->expectExceptionCompat(ValidationException::class);

        $options = new FCM\V1\Option();
        $options->validate();
    }

    public function testInvalidOption()
    {
        $this->expectExceptionCompat(OptionTypeError::class);

        new FCM\V1(new FakeOption());
    }
}
