<?php

namespace Sunaoka\PushNotifications\Drivers;

use Sunaoka\PushNotifications\Exceptions\ValidationException;
use Valitron\Validator;

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
     * @var string[][]
     */
    protected $validationRules = [];

    /**
     * @var string[][]
     */
    private $defaultValidationRules = [
        'payload' => ['required'],
    ];

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
        $validator = new Validator((array) $this);
        $validator->mapFieldsRules(array_merge($this->defaultValidationRules, $this->validationRules));
        if (! $validator->validate()) {
            // @phpstan-ignore argument.type
            throw new ValidationException($validator->errors());
        }

        return true;
    }
}
