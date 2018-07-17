<?php

declare(strict_types=1);

namespace Tests\Darkilliant\MqProcessBundle\Message\Publisher;

use Darkilliant\MqProcessBundle\Message\Message;
use Darkilliant\MqProcessBundle\Message\Publisher\AmqpMessagePublisher;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AmqpMessagePublisherTest extends TestCase
{
    /** @var AmqpMessagePublisher */
    private $publisher;

    /** @var AMQPChannel|MockObject */
    private $channel;

    protected function setUp()
    {
        $this->channel = $this->createMock(AMQPChannel::class);
        $this->publisher = new AmqpMessagePublisher($this->channel, 'demo_queue', 'demo_exchange', true);
    }

    public function testPublish()
    {
        $this->channel
            ->expects($this->exactly(5))
            ->method('batch_basic_publish')
            ->with(new AMQPMessage('{}'), 'demo_exchange', 'demo_queue');
        $this->channel
            ->expects($this->once())
            ->method('publish_batch');

        $this->publisher->publish(new Message('{}'), 5);
        $this->publisher->publish(new Message('{}'), 5);
        $this->publisher->publish(new Message('{}'), 5);
        $this->publisher->publish(new Message('{}'), 5);
        $this->publisher->publish(new Message('{}'), 5);
    }

    public function testFinalize()
    {
        $this->channel
            ->expects($this->once())
            ->method('publish_batch');

        $this->publisher->finalize();
    }

    public function testCountMessage()
    {
        $this->channel
            ->expects($this->once())
            ->method('queue_declare')
            ->with('demo_queue', true)
            ->willReturn([0, 5, 0]);

        $this->assertEquals(5, $this->publisher->countMessages());
    }
}