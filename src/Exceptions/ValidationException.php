<?php

namespace Sunaoka\PushNotifications\Exceptions;

use Exception;

class ValidationException extends Exception
{
    /**
     * @param array|string $errors
     */
    public function __construct($errors)
    {
        if (is_array($errors)) {
            $message = implode(', ', array_map(static function ($errors) {
                return implode(', ', $errors);
            }, $errors));
        } else {
            $message = $errors;
        }

        parent::__construct("{$message}.");
    }
}
