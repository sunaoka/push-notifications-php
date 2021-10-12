<?php

namespace Sunaoka\PushNotifications\Tests\Drivers\APNs;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Sunaoka\PushNotifications\Drivers\APNs;
use Sunaoka\PushNotifications\Exceptions\OptionTypeError;
use Sunaoka\PushNotifications\Exceptions\ValidationException;
use Sunaoka\PushNotifications\Pusher;
use Sunaoka\PushNotifications\Tests\Fake\FakeOption;
use Sunaoka\PushNotifications\Tests\TestCase;

class CertificateTest extends TestCase
{
    public function testSingleToken()
    {
        $payload = [
            'data' => [
                'key' => 'value',
            ],
        ];

        $options = new APNs\Certificate\Option();
        $options->payload = $payload;
        $options->certificate = $this->certs('/fake.pem');
        $options->password = 'password';
        $options->topic = 'com.example.app';

        $driver = new APNs\Certificate($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, ['apns-id' => '01234567-0123-0123-0123-01234567890A']),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to('1234567890')
            ->send($driver);

        self::assertTrue($feedback->isSuccess('1234567890'));
        self::assertSame('01234567-0123-0123-0123-01234567890A', $feedback->success('1234567890'));
    }

    public function testMultipleToken()
    {
        $payload = [
            'data' => [
                'key' => 'value',
            ],
        ];

        $options = new APNs\Certificate\Option();
        $options->payload = $payload;
        $options->certificate = $this->certs('/fake.pem');
        $options->password = 'password';
        $options->topic = 'com.example.app';

        $driver = new APNs\Certificate($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, ['apns-id' => '01234567-0123-0123-0123-01234567890A']),
                new Response(200, ['apns-id' => '01234567-0123-0123-0123-01234567890B']),
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
        self::assertSame('01234567-0123-0123-0123-01234567890A', $feedback->success('1234567890'));
        self::assertSame('01234567-0123-0123-0123-01234567890B', $feedback->success('abcdefghij'));
    }

    public function testSingleFailure()
    {
        $payload = [
            'data' => [
                'key' => 'value',
            ],
        ];

        $options = new APNs\Certificate\Option();
        $options->payload = $payload;
        $options->certificate = $this->certs('/fake.pem');
        $options->password = 'password';
        $options->topic = 'com.example.app';

        $driver = new APNs\Certificate($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(400, [], '{"reason": "BadDeviceToken"}'),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to('1234567890')
            ->send($driver);

        self::assertFalse($feedback->isSuccess('1234567890'));
        self::assertSame('BadDeviceToken', $feedback->failure('1234567890'));
    }

    public function testMultipleFailure()
    {
        $payload = [
            'data' => [
                'key' => 'value',
            ],
        ];

        $options = new APNs\Certificate\Option();
        $options->payload = $payload;
        $options->certificate = $this->certs('/fake.pem');
        $options->password = 'password';
        $options->topic = 'com.example.app';

        $driver = new APNs\Certificate($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, ['apns-id' => '01234567-0123-0123-0123-01234567890A']),
                new Response(400, [], '{"reason": "BadDeviceToken"}'),
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
        self::assertSame('01234567-0123-0123-0123-01234567890A', $feedback->success('1234567890'));
        self::assertSame('BadDeviceToken', $feedback->failure('abcdefghij'));
    }

    public function testMakeOption()
    {
        $payload = [
            'data' => [
                'key' => 'value',
            ],
        ];

        $options = new APNs\Certificate\Option([
            'payload'     => $payload,
            'certificate' => $this->certs('/fake.pem'),
            'password'    => 'password',
            'topic'       => 'com.example.app',
        ]);

        self::assertSame($payload, $options->payload);
        self::assertSame($this->certs('/fake.pem'), $options->certificate);
        self::assertSame('password', $options->password);
        self::assertSame('com.example.app', $options->topic);
    }

    public function testValidateOption()
    {
        $this->expectExceptionCompat(ValidationException::class);

        $options = new APNs\Certificate\Option();
        $options->validate();
    }

    public function testInvalidOption()
    {
        $this->expectExceptionCompat(OptionTypeError::class);

        new APNs\Certificate(new FakeOption());
    }

    public function testServerFailure()
    {
        $payload = [
            'data' => [
                'key' => 'value',
            ],
        ];

        $options = new APNs\Certificate\Option();
        $options->payload = $payload;
        $options->certificate = $this->certs('/fake.pem');
        $options->password = 'password';
        $options->topic = 'com.example.app';

        $driver = new APNs\Certificate($options);
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
}
