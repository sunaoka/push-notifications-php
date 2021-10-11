<?php

namespace Sunaoka\PushNotifications\Drivers\FCM;

use Exception;
use Google;
use GuzzleHttp;
use Sunaoka\PushNotifications\Drivers\Driver;
use Sunaoka\PushNotifications\Drivers\Feedback;
use Sunaoka\PushNotifications\Exceptions\OptionTypeError;

/**
 * @property V1\Option $options
 */
class V1 extends Driver
{
    /**
     * @var string
     */
    protected $endpointSandbox = 'https://fcm.googleapis.com/v1/projects/%s/messages:send';

    /**
     * @var string
     */
    protected $endpointProduction = 'https://fcm.googleapis.com/v1/projects/%s/messages:send';

    /**
     * @var Feedback
     */
    private $feedback;

    /**
     * @var GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @param V1\Option $options
     *
     * @throws OptionTypeError
     */
    public function __construct($options)
    {
        if (!$options instanceof V1\Option) {
            throw new OptionTypeError(V1\Option::class, $options);
        }

        $this->options = $options;
        $this->feedback = new Feedback();
    }

    /**
     * @return Feedback
     */
    public function send()
    {
        $client = new Google\Client([
            'credentials' => $this->options->credentials,
            'scopes'      => 'https://www.googleapis.com/auth/firebase.messaging',
        ]);

        $this->httpClient = $client->authorize($this->getHttpClient());

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
                'json' => array_merge_recursive(
                    $this->options->payload,
                    ['message' => ['token' => $device]]
                ),
            ];

            $response = $this->httpClient->post($this->getEndpoint($this->options->projectId), $options);

            $contents = json_decode($response->getBody()->getContents(), true);

            $this->feedback->addSuccess($device, $contents['name']);

            return;
        } catch (Exception $e) {
            $error = $this->parseErrorResponse($e);
        }

        if (isset($error['contents'])) {
            $json = json_decode($error['contents'], true);
            $this->feedback->addFailure($device, $json['error']['status']);
        } else {
            $this->feedback->addFailure($device, $error['message']);
        }
    }
}
