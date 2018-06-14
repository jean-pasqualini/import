<?php

namespace Darkilliant\MqProcessBundle\Message\Publisher;

use Darkilliant\MqProcessBundle\Message\Message;

interface MessagePublisherInterface
{
    public function publish(Message $message, int $batchCount);

    public function finalize();

    public function flush();

    public function countMessages(): int;
}
