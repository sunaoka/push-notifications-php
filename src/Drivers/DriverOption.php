<?php

namespace Sunaoka\PushNotifications\Drivers;

use Sunaoka\PushNotifications\Exceptions\ValidationException;
use Valitron\Validator;

abstract class DriverOption implements DriverOptionInterface
{
    /**
     * @var array
     */
    public $payload = [];

    /**
     * Guzzle Request Options
     *
     * <https://docs.guzzlephp.org/en/stable/request-options.html>
     *
     * @var array
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

    /**
     * @inheritDoc
     */
    public function __construct($options = [])
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @inheritDoc
     */
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
