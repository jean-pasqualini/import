<?php

declare(strict_types=1);

namespace Tests\Darkilliant\MqProcessBundle\Step;

use Darkilliant\MqProcessBundle\Message\Message;
use Darkilliant\MqProcessBundle\Message\Publisher\MessagePublisherFactory;
use Darkilliant\MqProcessBundle\Message\Publisher\MessagePublisherInterface;
use Darkilliant\MqProcessBundle\Step\SendMessageStep;
use Darkilliant\ProcessBundle\ProcessNotifier\ProgressBarProcessNotifier;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SendMessageStepTest extends TestCase
{
    /** @var SendMessageStep */
    private $step;

    /** @var MessagePublisherFactory|MockObject */
    private $messagePublisherFactory;

    /** @var MessagePublisherInterface|MockObject */
    private $messagePublisher;

    /** @var ProgressBarProcessNotifier|MockObject */
    private $processNotifier;

    public static function setUpBeforeClass()
    {
        ClockMock::register(SendMessageStep::class);
        ClockMock::withClockMock(true);
    }

    public function setUp()
    {
        $this->messagePublisher = $this->createMock(MessagePublisherInterface::class);
        $this->messagePublisherFactory = $this->createMock(MessagePublisherFactory::class);
        $this->processNotifier = $this->createMock(ProgressBarProcessNotifier::class);
        $this->step = new SendMessageStep($this->messagePublisherFactory, $this->processNotifier);
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
        $state->setData([]);
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
        ]);

        $this->messagePublisher
            ->expects($this->once())
            ->method('publish')
            ->with(new Message('[]'), 20);
        $this->messagePublisherFactory
            ->expects($this->once())
            ->method('factory')
            ->with('demo_exchange_demo_queue', $options)
            ->willReturn($this->messagePublisher);

        $this->step->execute($state);
    }

    public function testGetProgress()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setData([]);
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
        ]);
        $this->messagePublisherFactory
            ->expects($this->exactly(2))
            ->method('factory')
            ->with('demo_exchange_demo_queue', $options)
            ->willReturn($this->messagePublisher);
        $this->messagePublisher
            ->expects($this->exactly(2))
            ->method('countMessages')
            ->willReturnOnConsecutiveCalls(5, 2);

        $this->step->count($state);

        $this->assertEquals(3, $this->step->getProgress($state));
    }

    public function testFinalize()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setData([]);
        $state->setOptions($options = [
            'batch_count' => 20,
            'exchange' => 'demo_exchange',
            'queue' => 'demo_queue',
        ]);
        $this->messagePublisherFactory
            ->expects($this->exactly(3))
            ->method('factory')
            ->with('demo_exchange_demo_queue', $options)
            ->willReturn($this->messagePublisher);
        $this->messagePublisher
            ->expects($this->once())
            ->method('finalize');
        $this->processNotifier
            ->expects($this->once())
            ->method('onStartIterableProcess');
        $this->processNotifier
            ->expects($this->once())
            ->method('onUpdateIterableProcess')
            ->willReturnCallback(function() use ($state) {
               $this->step->getProgress($state);
            });
        $this->messagePublisher
            ->expects($this->exactly(2))
            ->method('countMessages')
            ->willReturnOnConsecutiveCalls(5, 0);

        $this->step->count($state);
        $this->step->finalize($state);
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
        ]);

        $this->messagePublisher
            ->expects($this->once())
            ->method('countMessages')
            ->willReturn(5);

        $this->messagePublisherFactory
            ->expects($this->once())
            ->method('factory')
            ->with('demo_exchange_demo_queue', $options)
            ->willReturn($this->messagePublisher);

        $this->assertEquals(5, $this->step->count($state));
    }
}