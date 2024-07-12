<?php

namespace Sunaoka\PushNotifications\Exceptions;

class OptionTypeError extends TypeError
{
    /**
     * @param string $expected
     * @param mixed  $actual
     */
    public function __construct($expected, $actual)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        if (isset($trace[1])) {
            // @phpstan-ignore offsetAccess.notFound
            $method = sprintf('%s::%s()', $trace[1]['class'], $trace[1]['function']);
        } else {
            $method = 'Unknown';  // @codeCoverageIgnore
        }

        $message = sprintf(
            '%s: Argument #1 ($options) must be of type %s, %s given, called',
            $method,
            $expected,
            is_object($actual) ? get_class($actual) : gettype($actual)
        );

        parent::__construct($message);
    }
}
