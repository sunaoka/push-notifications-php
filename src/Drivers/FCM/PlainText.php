<?php

namespace Sunaoka\PushNotifications\Drivers\FCM;

use Exception;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Sunaoka\PushNotifications\Drivers\Driver;
use Sunaoka\PushNotifications\Drivers\Feedback;
use Sunaoka\PushNotifications\Exceptions\OptionTypeError;

/**
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
        if (!$options instanceof PlainText\Option) {
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
        $this->httpClient = $this->getHttpClient();

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
        } catch (ClientException $e) {
            $message = $e->getResponse()->getReasonPhrase();
        } catch (ServerException $e) {
            $message = $e->getResponse()->getReasonPhrase();
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        $this->feedback->addFailure($device, $message);
    }
}
