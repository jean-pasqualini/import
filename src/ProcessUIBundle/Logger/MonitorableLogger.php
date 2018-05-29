<?php

declare(strict_types=1);

namespace Darkilliant\ProcessUIBundle\Logger;

use Psr\Log\AbstractLogger;

/**
 * @internal
 */
class MonitorableLogger extends AbstractLogger
{
    private $messages;

    private $handler;

    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    public function log($level, $message, array $context = [])
    {
        $message = ['level' => $level, 'message' => date('d/m/Y H:i:s').' '.$this->interpolate($message, $context), 'context' => $context];

        call_user_func_array($this->handler, [$message]);
    }

    private function interpolate($message, array $context = [])
    {
        // build a replacement array with braces around the context keys
        $replace = [];
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{'.$key.'}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}
