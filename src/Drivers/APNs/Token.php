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
 * @property Token\Option $options
 */
class Token extends Driver
{
    /**
     * @var string
     */
    protected $endpointSandbox = 'https://api.sandbox.push.apple.com/3/device/';

    /**
     * @var string
     */
    protected $endpointProduction = 'https://api.push.apple.com/3/device/';

    /**
     * @var Feedback
     */
    private $feedback;

    /**
     * @var GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @param Token\Option $options
     *
     * @throws OptionTypeError
     */
    public function __construct($options)
    {
        if (!$options instanceof Token\Option) {
            throw new OptionTypeError(Token\Option::class, $options);
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
                    'authorization' => $this->bearerToken(
                        $this->options->authKey,
                        $this->options->keyId,
                        $this->options->teamId
                    ),
                    'apns-topic'    => $this->options->topic,
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
            $message = $this->getReason($e->getResponse());
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        $this->feedback->addFailure($device, $message);
    }

    /**
     * @param string $authKey
     * @param string $keyId
     * @param string $teamId
     *
     * @return string
     */
    private function bearerToken($authKey, $keyId, $teamId)
    {
        $key = openssl_pkey_get_private($authKey);

        $header = $this->jwtEncode(['alg' => 'ES256', 'kid' => $keyId]);
        $claims = $this->jwtEncode(['iss' => $teamId, 'iat' => time()]);

        openssl_sign("{$header}.{$claims}", $signature, $key, 'sha256');

        return sprintf('bearer %s.%s.%s', $header, $claims, base64_encode($signature));
    }

    /**
     * @param array $input
     *
     * @return string
     */
    private function jwtEncode($input)
    {
        return str_replace('=', '', strtr(base64_encode(json_encode($input)), '+/', '-_'));
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
