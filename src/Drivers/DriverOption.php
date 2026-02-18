<?php

namespace Sunaoka\PushNotifications\Drivers;

use Sunaoka\PushNotifications\Exceptions\ValidationException;

abstract class DriverOption implements DriverOptionInterface
{
    /**
     * @var array<string, mixed>
     */
    public $payload = [];

    /**
     * Guzzle Request Options
     *
     * @var array<string, mixed>
     * @link https://docs.guzzlephp.org/en/stable/request-options.html
     */
    public $httpOptions = [];

    /**
     * @var string[]
     */
    protected $required = [];

    public function __construct($options = [])
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function validate()
    {
        $rules = array_merge([
            'payload',
        ], $this->required);

        $fails = [];
        foreach ($rules as $rule) {
            if (empty($this->{$rule})) {
                $fails[$rule] = ["{$rule} is required"];
            }
        }

        if (! empty($fails)) {
            throw new ValidationException($fails);
        }

        return true;
    }
}
