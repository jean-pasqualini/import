<?php

declare(strict_types=1);

namespace Darkilliant\MqProcessBundle\Message\Provider;

use Darkilliant\MqProcessBundle\Message\Message;

interface MessageProviderInterface
{
    public function consume($ackRequired = true);

    public function stopConsume();

    public function process($envelope);

    public function messageOk(Message $message);

    public function messageKo(Message $message, bool $requeue);

    public function waitChannel($blocking = true);

    public function fetchMessage();
}
