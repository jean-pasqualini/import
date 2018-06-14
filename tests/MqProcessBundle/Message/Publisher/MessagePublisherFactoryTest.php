<?php

declare(strict_types=1);

namespace Tests\Darkilliant\MqProcessBundle\Message\Publisher;

use Darkilliant\MqProcessBundle\Message\Publisher\AmqpMessagePublisher;
use Darkilliant\MqProcessBundle\Message\Publisher\MessagePublisherFactory;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class MessagePublisherFactoryTest extends TestCase
{
    /** @var MessagePublisherFactory */
    private $factory;

    /** @var ContainerInterface|MockObject */
    private $container;

    /** @var AMQPLazyConnection|MockObject */
    private $connection;

    protected function setUp()
    {
        $this->connection = $this->createMock(AMQPLazyConnection::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->factory = new MessagePublisherFactory($this->container);
    }

    public function testFactoryWithUnknowType()
    {
        $this->assertNull($this->factory->factory('unknow', [
            'client' => 'unknow',
        ]));
    }

    public function testFactoryWithAmqpLib()
    {
        $this->connection
            ->expects($this->once())
            ->method('channel')
            ->willReturn($this->createMock(AMQPChannel::class));

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('darkilliant_mqprocess_connection')
            ->willReturn($this->connection);

        $messageProvider = $this->factory->factory('demo', [
            'client' => 'amqp_lib',
            'queue' => 'demo_queue',
            'exchange' => 'exchange_queue',
            'persistant' => true
        ]);

        $this->assertInstanceOf(AmqpMessagePublisher::class, $messageProvider);
        $this->assertEquals($messageProvider, $this->factory->factory('demo', []));
    }
}