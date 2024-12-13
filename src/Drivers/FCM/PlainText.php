<?php

namespace Sunaoka\PushNotifications\Drivers\FCM;

use GuzzleHttp;
use Sunaoka\PushNotifications\Drivers\Driver;
use Sunaoka\PushNotifications\Drivers\Feedback;
use Sunaoka\PushNotifications\Exceptions\OptionTypeError;

/**
 * @deprecated HTTP legacy APIs was deprecated on June 20, 2023, and will be removed in June 2024.
 *
 * @property PlainText\Option $options
 */
class PlainText extends Driver
{
    /**
     * @var string
     */
    protected $endpointSandbox = 'https://fcm.googleapis.com/fcm/send';

    /**
     * @var string
     */
    protected $endpointProduction = 'https://fcm.googleapis.com/fcm/send';

    /**
     * @var Feedback
     */
    private $feedback;

    /**
     * @var GuzzleHttp\Client
     */
    private $httpClient;

    const SUCCESS_MAKER = 'id=';

    const FAILURE_MAKER = 'Error=';

    /**
     * @param PlainText\Option $options
     *
     * @throws OptionTypeError
     */
    public function __construct($options)
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (! $options instanceof PlainText\Option) {
            throw new OptionTypeError(PlainText\Option::class, $options);
        }

        $this->options = $options;
        $this->feedback = new Feedback();
    }

    /**
     * @return Feedback
     */
    public function send()
    {
        $this->httpClient = $this->getHttpClient($this->options->httpOptions);

        foreach ($this->devices as $device) {
            $this->_send($device);
        }

        return $this->feedback;
    }

    /**
     * @param string $device
     *
     * @return void
     */
    private function _send($device)
    {
        try {
            $options = [
                'headers'     => [
                    'Authorization' => "key={$this->options->apiKey}",
                ],
                'form_params' => array_merge(
                    $this->options->payload,
                    ['registration_id' => $device]
                ),
            ];

            $response = $this->httpClient->post($this->getEndpoint(), $options);

            $contents = $response->getBody()->getContents();

            if (strpos($contents, self::SUCCESS_MAKER) !== false) {
                $message = substr($contents, strlen(self::SUCCESS_MAKER));
                $this->feedback->addSuccess($device, $message);
            } elseif (strpos($contents, self::FAILURE_MAKER) !== false) {
                $message = substr($contents, strlen(self::FAILURE_MAKER));
                $this->feedback->addFailure($device, $message);
            }

            return;

        } catch (\Exception $e) {
            $error = $this->parseErrorResponse($e);
        }

        $this->feedback->addFailure($device, $error['message']);
    }
}
