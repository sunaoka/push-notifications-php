<?php

namespace Sunaoka\PushNotifications\Drivers\APNs;

use Exception;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
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
        } catch (ClientException $e) {
            $message = $this->getReason($e->getResponse());
        } catch (ServerException $e) {
            $message = $e->getResponse()->getReasonPhrase();
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        $this->feedback->addFailure($device, $message);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return string
     */
    private function getReason($response)
    {
        $contents = $response->getBody()->getContents();
        if (empty($contents)) {
            return $response->getReasonPhrase();
        }

        $json = json_decode($contents, true);
        if (!isset($json['reason'])) {
            return $response->getReasonPhrase();
        }

        return $json['reason'];
    }
}
