<?php

declare(strict_types=1);

namespace Darkilliant\MqProcessBundle\Message\Publisher;

use Darkilliant\MqProcessBundle\Message\Message;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpMessagePublisher implements MessagePublisherInterface
{
    /** @var AMQPChannel */
    private $channel;
    /** @var string */
    private $queue;
    /** @var string */
    private $exchange;
    /** @var int */
    private $batch = 0;
    /** @var bool */
    private $persistant;

    public function __construct(AMQPChannel $channel, string $queue, string $exchange, bool $persistant)
    {
        $this->channel = $channel;
        $this->queue = $queue;
        $this->exchange = $exchange;
        $this->persistant = $persistant;

        $this->channel->queue_declare($this->queue, false, $this->persistant, false, false);
        $this->channel->queue_bind($this->queue, $this->exchange, $this->queue);
    }

    public function publish(Message $message, int $batchCount)
    {
        ++$this->batch;
        $this->channel->batch_basic_publish($this->buildMessage($message), $this->exchange, $this->queue);

        if ($this->batch >= $batchCount) {
            $this->flush();
        }
    }

    public function finalize()
    {
        $this->flush();
    }

    public function flush()
    {
        $this->channel->publish_batch();
    }

    public function countMessages(): int
    {
        return $this->channel->queue_declare($this->queue, true)[1];
    }

    private function buildMessage(Message $message)
    {
        return new AMQPMessage($message->getBody());
    }
}
