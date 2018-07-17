<?php

declare(strict_types=1);

namespace Tests\Darkilliant\MqProcessBundle\Step;

use Darkilliant\MqProcessBundle\Message\Message;
use Darkilliant\MqProcessBundle\Message\Provider\MessageProviderFactory;
use Darkilliant\MqProcessBundle\Message\Provider\MessageProviderInterface;
use Darkilliant\MqProcessBundle\Step\ConsumeMessageStep;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsumeMessageStepTest extends TestCase
{
    /** @var ConsumeMessageStep */
    private $step;

    /** @var MessageProviderFactory|MockObject */
    private $messageProviderFactory;

    /** @var MessageProviderInterface|MockObject */
    private $messageProvider;

    protected function setUp()
    {
        $this->messageProvider = $this->createMock(MessageProviderInterface::class);
        $this->messageProviderFactory = $this->createMock(MessageProviderFactory::class);
        $this->step = new ConsumeMessageStep($this->messageProviderFactory);
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    public function testExecute()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
            'ack_required' => true,
        ]);

        $this->messageProvider
            ->expects($this->once())
            ->method('consume');
        $this->messageProviderFactory
            ->expects($this->once())
            ->method('factory')
            ->with('demo_queue', $options)
            ->willReturn($this->messageProvider);

        $this->step->execute($state);
        $this->assertEquals('demo_queue', $state->getContext('queue'));
    }

    public function testGetProgress()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
            'ack_required' => true,
        ]);

        $this->assertEquals(50, $this->step->getProgress($state));
    }

    public function testCount()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
            'ack_required' => true,
        ]);

        $this->assertEquals(100, $this->step->count($state));
    }

    public function testValid()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
            'ack_required' => true,
        ]);

        $this->assertTrue($this->step->valid($state));
    }

    public function testNext()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
            'ack_required' => true,
        ]);

        $this->messageProvider
            ->expects($this->once())
            ->method('fetchMessage')
            ->willReturn(new Message('{"color":"red"}'));
        $this->messageProviderFactory
            ->expects($this->once())
            ->method('factory')
            ->with('demo_queue', $options)
            ->willReturn($this->messageProvider);

        $this->step->next($state);
        $this->assertEquals(['color' => 'red'], $state->getData());
    }

    public function testNextWhenWaitChannel()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
            'ack_required' => true,
        ]);

        $this->messageProvider
            ->expects($this->exactly(2))
            ->method('fetchMessage')
            ->willReturnOnConsecutiveCalls(null, new Message('{"color":"red"}'));
        $this->messageProvider
            ->expects($this->once())
            ->method('waitChannel');
        $this->messageProviderFactory
            ->expects($this->exactly(3))
            ->method('factory')
            ->with('demo_queue', $options)
            ->willReturn($this->messageProvider);

        $this->step->next($state);
        $this->assertEquals(['color' => 'red'], $state->getData());
    }

    public function testOnFailledLoop()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
            'ack_required' => true,
            'requeue_on_fail' => true,
        ]);

        $this->messageProvider
            ->expects($this->once())
            ->method('fetchMessage')
            ->willReturn(new Message('{"color":"red"}'));
        $this->messageProvider
            ->expects($this->once())
            ->method('messageKo')
            ->with(new Message('{"color":"red"}'), true);
        $this->messageProviderFactory
            ->expects($this->exactly(2))
            ->method('factory')
            ->with('demo_queue', $options)
            ->willReturn($this->messageProvider);

        $this->step->onFailedLoop($state);
    }

    public function testOnFailedLoopWhenNotAck()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'ack_required' => false,
        ]);

        $this->messageProviderFactory
            ->expects($this->never())
            ->method('factory');

        $this->step->onFailedLoop($state);
    }

    public function testOnSuccessLoop()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
            'ack_required' => true,
        ]);

        $this->messageProvider
            ->expects($this->exactly(1))
            ->method('fetchMessage')
            ->willReturn(new Message('{"color":"red"}'));
        $this->messageProvider
            ->expects($this->once())
            ->method('messageOk')
            ->with(new Message('{"color":"red"}'));
        $this->messageProviderFactory
            ->expects($this->exactly(2))
            ->method('factory')
            ->with('demo_queue', $options)
            ->willReturn($this->messageProvider);

        $this->step->next($state);
        $this->step->onSuccessLoop($state);
    }

    public function testOnSuccessLoopWhenNotAck()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'ack_required' => false,
        ]);

        $this->messageProviderFactory
            ->expects($this->never())
            ->method('factory');

        $this->step->onSuccessLoop($state);
    }
}