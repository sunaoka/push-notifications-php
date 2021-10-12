<?php

namespace Sunaoka\PushNotifications\Tests\Drivers\FCM;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Sunaoka\PushNotifications\Drivers\FCM;
use Sunaoka\PushNotifications\Exceptions\OptionTypeError;
use Sunaoka\PushNotifications\Exceptions\ValidationException;
use Sunaoka\PushNotifications\Pusher;
use Sunaoka\PushNotifications\Tests\Fake\FakeOption;
use Sunaoka\PushNotifications\Tests\TestCase;

class JsonTest extends TestCase
{
    public function testSingleToken()
    {
        $payload = [
            'notification' => [
                'title' => 'title',
            ],
        ];

        $options = new FCM\Json\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\Json($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], json_encode([
                    'multicast_id'  => 1234567890123456789,
                    'success'       => 1,
                    'failure'       => 0,
                    'canonical_ids' => 0,
                    'results'       => [
                        ['message_id' => '0:1632441600000000%d00000000000000a'],
                    ],
                ])),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to('1234567890')
            ->send($driver);

        self::assertTrue($feedback->isSuccess('1234567890'));
        self::assertSame('0:1632441600000000%d00000000000000a', $feedback->success('1234567890'));
    }

    public function testMultipleToken()
    {
        $payload = [
            'notification' => [
                'title' => 'title',
            ],
        ];

        $options = new FCM\Json\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\Json($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], json_encode([
                    'multicast_id'  => 1234567890123456789,
                    'success'       => 2,
                    'failure'       => 0,
                    'canonical_ids' => 0,
                    'results'       => [
                        ['message_id' => '0:1632441600000000%d00000000000000a'],
                        ['message_id' => '0:1632441600000000%d00000000000000b'],
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

        self::assertTrue($feedback->isSuccess('1234567890'));
        self::assertTrue($feedback->isSuccess('abcdefghij'));
        self::assertSame('0:1632441600000000%d00000000000000a', $feedback->success('1234567890'));
        self::assertSame('0:1632441600000000%d00000000000000b', $feedback->success('abcdefghij'));
    }

    public function testSingleFailure()
    {
        $payload = [
            'notification' => [
                'title' => 'title',
            ],
        ];

        $options = new FCM\Json\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\Json($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], json_encode([
                    'multicast_id'  => 1234567890123456789,
                    'success'       => 0,
                    'failure'       => 1,
                    'canonical_ids' => 0,
                    'results'       => [
                        ['error' => 'InvalidRegistration'],
                    ],
                ])),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to('1234567890')
            ->send($driver);

        self::assertFalse($feedback->isSuccess('1234567890'));
        self::assertSame('InvalidRegistration', $feedback->failure('1234567890'));
    }

    public function testMultipleFailure()
    {
        $payload = [
            'notification' => [
                'title' => 'title',
            ],
        ];

        $options = new FCM\Json\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\Json($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], json_encode([
                    'multicast_id'  => 1234567890123456789,
                    'success'       => 0,
                    'failure'       => 1,
                    'canonical_ids' => 0,
                    'results'       => [
                        ['message_id' => '0:1632441600000000%d00000000000000a'],
                        ['error' => 'InvalidRegistration'],
                    ],
                ])),
                new Response(200, [], json_encode([
                    'multicast_id'  => 1234567890123456789,
                    'success'       => 0,
                    'failure'       => 1,
                    'canonical_ids' => 0,
                    'results'       => [
                        ['message_id' => '0:1632441600000000%d00000000000000a'],
                        ['error' => 'InvalidRegistration'],
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

        self::assertTrue($feedback->isSuccess('1234567890'));
        self::assertFalse($feedback->isSuccess('abcdefghij'));
        self::assertSame('0:1632441600000000%d00000000000000a', $feedback->success('1234567890'));
        self::assertSame('InvalidRegistration', $feedback->failure('abcdefghij'));
    }

    public function testMakeOption()
    {
        $payload = [
            'notification' => [
                'title' => 'title',
            ],
        ];

        $options = new FCM\Json\Option([
            'payload' => $payload,
            'apiKey'  => 'fake-api-key',
        ]);

        self::assertSame($payload, $options->payload);
        self::assertSame('fake-api-key', $options->apiKey);
    }

    public function testValidateOption()
    {
        $this->expectExceptionCompat(ValidationException::class);

        $options = new FCM\Json\Option();
        $options->validate();
    }

    public function testInvalidOption()
    {
        $this->expectExceptionCompat(OptionTypeError::class);

        new FCM\Json(new FakeOption());
    }

    public function testRequestFailure()
    {
        $payload = [
            'notification' => [
                'title' => 'title',
            ],
        ];

        $options = new FCM\Json\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\Json($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(500),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to('1234567890')
            ->send($driver);

        self::assertFalse($feedback->isSuccess('1234567890'));
        self::assertSame('Internal Server Error', $feedback->failure('1234567890'));
    }

    public function testRequestException()
    {
        $payload = [
            'notification' => [
                'title' => 'title',
            ],
        ];

        $options = new FCM\Json\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\Json($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new RequestException('Error Communicating with Server', new Request('POST', '/')),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to('1234567890')
            ->send($driver);

        self::assertFalse($feedback->isSuccess('1234567890'));
        self::assertSame('Error Communicating with Server', $feedback->failure('1234567890'));
    }
}
