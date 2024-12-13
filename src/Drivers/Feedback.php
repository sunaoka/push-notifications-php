<?php

namespace Sunaoka\PushNotifications\Drivers;

class Feedback
{
    /**
     * @var array<string, string>
     */
    private $success;

    /**
     * @var array<string, string>
     */
    private $failure;

    /**
     * @param array<string, string> $success
     * @param array<string, string> $failure
     */
    public function __construct($success = [], $failure = [])
    {
        $this->set($success, $failure);
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->set([], []);
    }

    /**
     * @param array<string, string> $success
     * @param array<string, string> $failure
     *
     * @return void
     */
    private function set($success, $failure)
    {
        $this->success = $success;
        $this->failure = $failure;
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

    /**
     * @param string $token
     *
     * @return bool
     */
    public function isSuccess($token)
    {
        return isset($this->success[$token]);
    }
}
