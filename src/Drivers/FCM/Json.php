<?php

namespace Sunaoka\PushNotifications\Drivers\FCM;

use GuzzleHttp;
use Sunaoka\PushNotifications\Drivers\Driver;
use Sunaoka\PushNotifications\Drivers\Feedback;
use Sunaoka\PushNotifications\Exceptions\OptionTypeError;

/**
 * @deprecated HTTP legacy APIs was deprecated on June 20, 2023, and will be removed in June 2024.
 *
 * @property Json\Option $options
 */
class Json extends Driver
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

    /**
     * @param Json\Option $options
     *
     * @throws OptionTypeError
     */
    public function __construct($options)
    {
        // @phpstan-ignore instanceof.alwaysTrue
        if (! $options instanceof Json\Option) {
            throw new OptionTypeError(Json\Option::class, $options);
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

        try {
            $options = [
                'headers' => [
                    'Authorization' => "key={$this->options->apiKey}",
                ],
                'json'    => array_merge(
                    $this->options->payload,
                    ['registration_ids' => $this->devices]
                ),
            ];

            $response = $this->httpClient->post($this->getEndpoint(), $options);

            /** @var array{results: array<int, array{message_id?: string, error?: string}>} $contents */
            $contents = json_decode($response->getBody()->getContents(), true);

            foreach ($this->devices as $index => $device) {
                $result = $contents['results'][$index];
                if (isset($result['message_id'])) {
                    $this->feedback->addSuccess($device, $result['message_id']);
                } elseif (isset($result['error'])) {
                    $this->feedback->addFailure($device, $result['error']);
                }
            }

            return $this->feedback;

        } catch (\Exception $e) {
            $error = $this->parseErrorResponse($e);
        }

        foreach ($this->devices as $device) {
            $this->feedback->addFailure($device, $error['message']);
        }

        return $this->feedback;
    }
}
