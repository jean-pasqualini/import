<?php

declare(strict_types=1);

namespace Tests\Darkilliant\MqProcessBundle\Message\Provider;

use Darkilliant\MqProcessBundle\Message\Message;
use Darkilliant\MqProcessBundle\Message\Provider\AmqpMessageProvider;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AmqpMessageProviderTest extends TestCase
{
    /** @var AmqpMessageProvider|MockObject */
    private $provider;
    /** @var AMQPChannel|MockObject */
    private $channel;

    public function setUp()
    {
        $this->channel = $this->createMock(AMQPChannel::class);
        $this->provider = $this
            ->getMockBuilder(AmqpMessageProvider::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([
                $this->channel,
                'one_queue',
                'one_exchange',
                true,
                5000
            ])
            ->setMethods(['getConsumerTag'])
            ->getMock();
        $this->provider
            ->method('getConsumerTag')
            ->willReturn('customer_tag');
    }

    public function testFetchMessageWhenNoMessage()
    {
        $this->assertNull($this->provider->fetchMessage());
    }

    public function testFetchMessageWhenOneMessage()
    {
        $message = new AMQPMessage('', [
            'application_headers' => [
                'x-debug' => ['x-debug', 1],
            ],
        ]);
        $message->delivery_info['delivery_tag'] = 5;
        $this->provider->process($message);

        /** @var Message $finalMessage */
        $finalMessage = $this->provider->fetchMessage();
        $this->assertEquals(5, $finalMessage->getId());
        $this->assertEquals([
            'headers' => ['x-debug' => 1],
            'delivery_tag' => 5,
        ], $finalMessage->getProperties());
    }

    public function testConsume()
    {
        $this->channel
            ->expects($this->once())
            ->method('basic_consume')
            ->with(
                'one_queue',
                'customer_tag',
                false,
                false,
                false,
                false,
                [$this->provider, 'process']
            );

        $this->provider->consume();
    }

    public function testStopConsume()
    {
        $this->channel
            ->expects($this->once())
            ->method('basic_cancel')
            ->with(
                'one_queue'
            );

        $this->provider->stopConsume();
    }

    public function testMessageOk()
    {
        $message = new Message('{}', [], 5);

        $this->channel
            ->expects($this->once())
            ->method('basic_ack')
            ->with(
                5
            );

        $this->provider->messageOk($message);
    }

    public function testMessageKo()
    {
        $message = new Message('{}', [], 5);

        $this->channel
            ->expects($this->once())
            ->method('basic_nack')
            ->with(
                5,
                false,
                true
            );

        $this->provider->messageKo($message, true);
    }

    public function testProcessNull()
    {
        $this->provider->process(null);
        $this->assertNull($this->provider->fetchMessage());
    }

    public function testWaitChannel()
    {
        $this->channel
            ->expects($this->once())
            ->method('wait')
            ->with(null, false);

        $this->provider->waitChannel();
    }
}
