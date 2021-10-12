<?php

namespace Sunaoka\PushNotifications\Drivers\APNs;

use Exception;
use GuzzleHttp;
use Sunaoka\PushNotifications\Drivers\Driver;
use Sunaoka\PushNotifications\Drivers\Feedback;
use Sunaoka\PushNotifications\Exceptions\OptionTypeError;

/**
 * @property Certificate\Option $options
 */
class Certificate extends Driver
{
    /**
     * @var string
     */
    protected $endpointSandbox = 'https://api.sandbox.push.apple.com/3/device/%s';

    /**
     * @var string
     */
    protected $endpointProduction = 'https://api.push.apple.com/3/device/%s';

    /**
     * @var Feedback
     */
    private $feedback;

    /**
     * @var GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @param Certificate\Option $options
     *
     * @throws OptionTypeError
     */
    public function __construct($options)
    {
        if (!$options instanceof Certificate\Option) {
            throw new OptionTypeError(Certificate\Option::class, $options);
        }

        $this->options = $options;
        $this->feedback = new Feedback();
    }

    /**
     * @return Feedback
     */
    public function send()
    {
        $this->httpClient = $this->getHttpClient([
            'version' => 2.0,
            'cert'    => [
                $this->options->certificate,
                $this->options->password,
            ],
        ]);

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
                'headers' => [
                    'apns-topic' => $this->options->topic,
                ],
                'body'    => json_encode($this->options->payload),
            ];

            $response = $this->httpClient->post($this->getEndpoint($device), $options);

            $apnsId = $response->getHeaderLine('apns-id');
            $this->feedback->addSuccess($device, $apnsId);

            return;
        } catch (Exception $e) {
            $error = $this->parseErrorResponse($e);
        }

        if (isset($error['contents'])) {
            $json = json_decode($error['contents'], true);
            $this->feedback->addFailure($device, $json['reason']);
        } else {
            $this->feedback->addFailure($device, $error['message']);
        }
    }
}
