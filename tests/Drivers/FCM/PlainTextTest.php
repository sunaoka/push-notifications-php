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

class PlainTextTest extends TestCase
{
    /**
     * @return void
     *
     * @throws OptionTypeError
     * @throws ValidationException
     */
    public function testSingleToken()
    {
        $payload = [
            'data.key' => 'value',
        ];

        $options = new FCM\PlainText\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\PlainText($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], 'id=0:1632441600000000%d00000000000000a'),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to('1234567890')
            ->send($driver);

        self::assertTrue($feedback->isSuccess('1234567890'));
        self::assertSame('0:1632441600000000%d00000000000000a', $feedback->success('1234567890'));
    }

    /**
     * @return void
     *
     * @throws OptionTypeError
     * @throws ValidationException
     */
    public function testMultipleToken()
    {
        $payload = [
            'data.key' => 'value',
        ];

        $options = new FCM\PlainText\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\PlainText($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], 'id=0:1632441600000000%d00000000000000a'),
                new Response(200, [], 'id=0:1632441600000000%d00000000000000b'),
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

    /**
     * @return void
     *
     * @throws OptionTypeError
     * @throws ValidationException
     */
    public function testSingleFailure()
    {
        $payload = [
            'data.key' => 'value',
        ];

        $options = new FCM\PlainText\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\PlainText($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], 'Error=MissingRegistration'),
            ])
        ));

        $pusher = new Pusher();
        $feedback = $pusher->to('1234567890')
            ->send($driver);

        self::assertFalse($feedback->isSuccess('1234567890'));
        self::assertSame('MissingRegistration', $feedback->failure('1234567890'));
    }

    /**
     * @return void
     *
     * @throws OptionTypeError
     * @throws ValidationException
     */
    public function testMultipleFailure()
    {
        $payload = [
            'data.key' => 'value',
        ];

        $options = new FCM\PlainText\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\PlainText($options);
        $driver->setHttpHandler(HandlerStack::create(
            new MockHandler([
                new Response(200, [], 'id=0:1632441600000000%d00000000000000a'),
                new Response(200, [], 'Error=MissingRegistration'),
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
        self::assertSame('MissingRegistration', $feedback->failure('abcdefghij'));
    }

    /**
     * @return void
     */
    public function testMakeOption()
    {
        $payload = [
            'data.key' => 'value',
        ];

        $options = new FCM\PlainText\Option([
            'payload' => $payload,
            'apiKey'  => 'fake-api-key',
        ]);

        self::assertSame($payload, $options->payload);
        self::assertSame('fake-api-key', $options->apiKey);
    }

    /**
     * @return void
     *
     * @throws ValidationException
     */
    public function testValidateOption()
    {
        $this->expectExceptionCompat(ValidationException::class);

        $options = new FCM\PlainText\Option();
        $options->validate();
    }

    /**
     * @return void
     *
     * @throws OptionTypeError
     */
    public function testInvalidOption()
    {
        $this->expectExceptionCompat(OptionTypeError::class);

        new FCM\PlainText(new FakeOption());  // @phpstan-ignore argument.type
    }

    /**
     * @return void
     *
     * @throws OptionTypeError
     * @throws ValidationException
     */
    public function testRequestFailure()
    {
        $payload = [
            'data.key' => 'value',
        ];

        $options = new FCM\PlainText\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\PlainText($options);
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

    /**
     * @return void
     *
     * @throws OptionTypeError
     * @throws ValidationException
     */
    public function testRequestException()
    {
        $payload = [
            'data.key' => 'value',
        ];

        $options = new FCM\PlainText\Option();
        $options->payload = $payload;
        $options->apiKey = 'fake-api-key';

        $driver = new FCM\PlainText($options);
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
