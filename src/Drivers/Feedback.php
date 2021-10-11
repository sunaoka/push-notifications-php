<?php

namespace Sunaoka\PushNotifications\Drivers;

class Feedback
{
    /**
     * @var array
     */
    private $success;

    /**
     * @var array
     */
    private $failure;

    /**
     * @param array $success
     * @param array $failure
     */
    public function __construct($success = [], $failure = [])
    {
        $this->success = $success;
        $this->failure = $failure;
    }

    /**
     * @param array $success
     * @param array $failure
     *
     * @return self
     */
    public static function make($success = [], $failure = [])
    {
        return new self($success, $failure);
    }

    /**
     * @param string $token
     * @param string $message
     *
     * @return void
     */
    public function addSuccess($token, $message)
    {
        $this->success[$token] = $message;
    }

    /**
     * @param string $token
     * @param string $message
     *
     * @return void
     */
    public function addFailure($token, $message)
    {
        $this->failure[$token] = $message;
    }

    /**
     * @param string $token
     *
     * @return string|null
     */
    public function success($token)
    {
        return isset($this->success[$token]) ? $this->success[$token] : null;
    }

    /**
     * @param string $token
     *
     * @return string|null
     */
    public function failure($token)
    {
        return isset($this->failure[$token]) ? $this->failure[$token] : null;
    }
}
