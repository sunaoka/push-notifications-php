<?php

namespace Sunaoka\PushNotifications\Drivers;

use Exception;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

abstract class Driver implements DriverInterface
{
    /**
     * @var string
     */
    protected $endpointSandbox = '';

    /**
     * @var string
     */
    protected $endpointProduction = '';

    /**
     * @var array
     */
    protected $devices;

    /**
     * @var bool
     */
    protected $production = false;

    /**
     * @var callable|null
     */
    protected $httpHandler = null;

    /**
     * @var DriverOptionInterface
     */
    protected $options = null;

    /**
     * @return DriverOptionInterface
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $devices
     *
     * @return self
     */
    public function to($devices)
    {
        $this->devices = array_values(array_unique($devices));

        return $this;
    }

    /**
     * @param bool $production
     *
     * @return self
     */
    public function setProduction($production)
    {
        $this->production = $production;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProduction()
    {
        return $this->production;
    }

    /**
     * @param callable|null $httpHandler
     *
     * @return self
     */
    public function setHttpHandler($httpHandler)
    {
        $this->httpHandler = $httpHandler;

        return $this;
    }

    /**
     * @param array $config
     *
     * @return GuzzleHttp\Client
     */
    protected function getHttpClient(array $config = [])
    {
        return new GuzzleHttp\Client(array_merge($config, [
            'handler' => $this->httpHandler,
        ]));
    }

    /**
     * @param string $replace
     *
     * @return string
     */
    protected function getEndpoint($replace = '')
    {
        return sprintf(($this->isProduction() ? $this->endpointProduction : $this->endpointSandbox), $replace);
    }

    /**
     * @param Exception $e
     *
     * @return array{message: string, contents: ?string}
     */
    protected function parseErrorResponse($e)
    {
        $contents = null;

        if ($e instanceof ClientException || $e instanceof ServerException) {
            $response = $e->getResponse();
            if ($response !== null) {
                $message = $response->getReasonPhrase();
                $contents = $response->getBody()->getContents();
            } else {
                $message = $e->getMessage();
            }
        } else {
            $message = $e->getMessage();
        }

        return [
            'message'  => $message,
            'contents' => !empty($contents) ? $contents : null,
        ];
    }
}
