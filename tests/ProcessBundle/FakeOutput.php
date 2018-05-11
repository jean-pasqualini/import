<?php

namespace Tests\Darkilliant\ProcessBundle;

use Symfony\Component\Console\Output\Output;

class FakeOutput extends Output
{
    private $buffer = '';

    /**
     * Empties buffer and returns its content.
     *
     * @return string
     */
    public function fetch()
    {
        return $this->buffer;
    }

    public function clearBuffer()
    {
        $this->buffer = '';
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $newline)
    {
        $this->buffer .= $message;

        if ($newline) {
            $this->buffer .= PHP_EOL;
        }
    }
}