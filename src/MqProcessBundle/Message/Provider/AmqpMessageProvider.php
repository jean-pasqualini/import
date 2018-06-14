<?php

declare(strict_types=1);

namespace Darkilliant\MqProcessBundle\Message\Provider;

use PhpAmqpLib\Channel\AMQPChannel;
use Darkilliant\MqProcessBundle\Message\Message;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpMessageProvider implements MessageProviderInterface
{
    /** @var AMQPChannel */
    private $channel;
    /** @var string */
    private $queue;
    /** @var array */
    private $messages = [];
    /** @var string */
    private $consumerTag;

    public function __construct(AMQPChannel $channel, string $queue, string $exchange, bool $persistant, int $batchCount)
    {
        $this->channel = $channel;
        $this->queue = $queue;

        $this->channel->queue_declare($this->queue, false, $persistant, false, false);
        $this->channel->queue_bind($queue, $exchange, $this->queue);
        $this->channel->basic_qos(null, 5000, false);
    }

    public function consume($ackRequired = true)
    {
        $this->channel->basic_consume(
            $this->queue,
            $this->getConsumerTag(),
            // when true, the server will not send messages to the connection that published them
            false,
            // when true, disable acknowledgements (more speed)
            !$ackRequired,
            // when true, queues may only be accessed by the current connection
            false,
            // when true, the server will not respond to the method.
            false,
            [$this, 'process'] //callback
        );
    }

    public function stopConsume()
    {
        $this->channel->basic_cancel($this->queue);
    }

    public function process($envelope)
    {
        if (null === $envelope) {
            return;
        }

        $this->messages[] = $message = $this->buildMessage($envelope);
    }

    public function messageOk(Message $message)
    {
        $this->channel->basic_ack($message->getId());
    }

    public function messageKo(Message $message, bool $requeue)
    {
        $this->channel->basic_nack($message->getId(), false, $requeue);
    }

    public function waitChannel($blocking = true)
    {
        $this->channel->wait(null, !$blocking);
    }

    public function fetchMessage()
    {
        return array_shift($this->messages);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getConsumerTag(): string
    {
        if (null === $this->consumerTag) {
            $this->consumerTag = sprintf('mqprocess_%s_%s_%s', $this->queue, time(), uniqid());
        }

        return $this->consumerTag;
    }

    private function buildMessage(AMQPMessage $envelope): Message
    {
        $properties = [];
        $propertyKeys = [
            'content_type', 'delivery_mode', 'content_encoding', 'type', 'timestamp', 'priority', 'expiration',
            'app_id', 'message_id', 'reply_to', 'correlation_id', 'user_id', 'cluster_id', 'channel', 'consumer_tag',
            'delivery_tag', 'redelivered', 'exchange', 'routing_key',
        ];

        foreach ($propertyKeys as $key) {
            if ($envelope->has($key)) {
                $properties[$key] = $envelope->get($key);
            }
        }

        $properties['headers'] = [];
        if ($envelope->has('application_headers')) {
            foreach ($envelope->get('application_headers') as $key => $value) {
                $properties['headers'][$key] = $value[1];
            }
        }

        return new Message($envelope->body, $properties, $envelope->get('delivery_tag'));
    }
}
