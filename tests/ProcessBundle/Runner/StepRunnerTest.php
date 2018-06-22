<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Runner;

use App\Step\DeprecatedStep;
use Darkilliant\ProcessBundle\Configuration\ConfigurationProcess;
use Darkilliant\ProcessBundle\Console\ProgressBar;
use Darkilliant\ProcessBundle\ProcessNotifier\ProgressBarProcessNotifier;
use Darkilliant\ProcessBundle\Registry\LoggerRegistry;
use Darkilliant\ProcessBundle\Registry\StepRegistry;
use Darkilliant\ProcessBundle\Resolver\OptionDynamicValueResolver;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\DebugStep;
use Darkilliant\ProcessBundle\Step\IterateArrayStep;
use Darkilliant\ProcessBundle\Step\PredefinedDataStep;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

class StepRunnerTest extends TestCase
{
    /** @var StepRunner */
    private $runner;

    /** @var LoggerRegistry|MockObject */
    private $loggerRegistry;

    /** @var StepRegistry|MockObject */
    private $stepRegistry;

    /** @var OptionDynamicValueResolver|MockObject */
    private $optionDynamicValueResolver;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var ProgressBarProcessNotifier|MockObject */
    private $progressBarNotifier;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->runner = new StepRunner(
            $this->loggerRegistry = $this->createMock(LoggerRegistry::class),
            $this->stepRegistry = $this->createMock(StepRegistry::class),
            $this->optionDynamicValueResolver = $this->createMock(OptionDynamicValueResolver::class),
            [
                'process' => [
                    'chocapic' => [
                        'logger' => 'monolog.logger.chocapic',
                        'steps' => [
                            [
                                'service' => PredefinedDataStep::class,
                                'options' => [
                                    'data' => [
                                        ['name' => 'john'],
                                        ['name' => 'gates'],
                                    ]
                                ]
                            ],
                            [
                                'service' => IterateArrayStep::class,
                                'options' => [],
                                'children' => [
                                    [
                                        'service' => DebugStep::class,
                                        'options' => [],
                                    ]
                                ]
                            ],
                        ]
                    ],
                    'deprecated' => [
                        'deprecated' => [DeprecatedStep::class],
                        'logger' => 'monolog.logger.deprecated',
                        'steps' => [
                            [
                                'service' => DeprecatedStep::class,
                                'options' => [],
                            ],
                            [
                                'service' => DebugStep::class,
                                'options' => [],
                                'enabled' => false,
                            ]
                        ]
                    ]
                ]
            ],
            $this->logger = $this->createMock(LoggerInterface::class),
            $this->progressBarNotifier = $this->createMock(ProgressBarProcessNotifier::class)
        );

        $this->optionDynamicValueResolver
            ->expects($this->any())
            ->method('resolve')
            ->willReturnArgument(0);
    }

    public function testSetNotifier()
    {
        $this->assertNull($this->runner->setNotifier($this->createMock(ProgressBarProcessNotifier::class)));
    }

    public function testBuildConfigurationProcessWhenProcessNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('process unknow not found, available (chocapic, deprecated)');

        $this->runner->buildConfigurationProcess('unknow');
    }

    public function testBuildConfigurationProcessWhenProcessExist()
    {
        $process = $this->runner->buildConfigurationProcess('chocapic');

        $this->assertInstanceOf(ConfigurationProcess::class, $process);
    }

    /**
     * @throws \Exception
     */
    public function testRun()
    {
        $this->loggerRegistry
            ->expects($this->once())
            ->method('resolveService')
            ->with('monolog.logger.chocapic')
            ->willReturn(new NullLogger());

        $this->stepRegistry
            ->expects($this->at(0))
            ->method('resolveService')
            ->with(PredefinedDataStep::class)
            ->willReturn(new PredefinedDataStep());
        $this->stepRegistry
            ->expects($this->at(1))
            ->method('resolveService')
            ->with(IterateArrayStep::class)
            ->willReturn(new IterateArrayStep());
        // Item one
        $this->stepRegistry
            ->expects($this->at(2))
            ->method('resolveService')
            ->with(DebugStep::class)
            ->willReturn(new DebugStep());
        // Item two
        $this->stepRegistry
            ->expects($this->at(3))
            ->method('resolveService')
            ->with(DebugStep::class)
            ->willReturn(new DebugStep());
        // Finalize
        $this->stepRegistry
            ->expects($this->at(4))
            ->method('resolveService')
            ->with(DebugStep::class)
            ->willReturn(new DebugStep());

        $this->runner->run($this->runner->buildConfigurationProcess('chocapic'), []);
    }

    /**
     * @throws \Exception
     */
    public function testRunWithDeprecatedStep()
    {
        $logger = $this->createMock(LoggerInterface::class);

        $this->loggerRegistry
            ->expects($this->once())
            ->method('resolveService')
            ->with('monolog.logger.deprecated')
            ->willReturn($logger);

        $this->stepRegistry
            ->expects($this->at(0))
            ->method('resolveService')
            ->with(DeprecatedStep::class)
            ->willReturn(new DeprecatedStep());

        $logger
            ->expects($this->once())
            ->method('log')
            ->with(LogLevel::WARNING, 'DEPRECATED STEPS USED', ['deprecated' => [DeprecatedStep::class]]);

        $this->runner->run($this->runner->buildConfigurationProcess('deprecated'), []);
    }

    /**
     * @throws \Exception
     */
    public function testRunWithExceptionInFinalize()
    {
        $this->loggerRegistry
            ->expects($this->once())
            ->method('resolveService')
            ->with('monolog.logger.chocapic')
            ->willReturn($this->logger);

        $this->stepRegistry
            ->expects($this->at(0))
            ->method('resolveService')
            ->with(PredefinedDataStep::class)
            ->willReturn(new PredefinedDataStep());
        $this->stepRegistry
            ->expects($this->at(1))
            ->method('resolveService')
            ->with(IterateArrayStep::class)
            ->willReturn(new IterateArrayStep());
        // Item one
        $this->stepRegistry
            ->expects($this->at(2))
            ->method('resolveService')
            ->with(DebugStep::class)
            ->willReturn(new DebugStep());
        // Item two
        $this->stepRegistry
            ->expects($this->at(3))
            ->method('resolveService')
            ->with(DebugStep::class)
            ->willReturn(new DebugStep());

        $step = $this->createMock(DebugStep::class);
        $step
            ->expects($this->once())
            ->method('finalize')
            ->willThrowException(new \Exception());

        $this->logger
            ->expects($this->once())
            ->method('error');

        // Finalize
        $this->stepRegistry
            ->expects($this->at(4))
            ->method('resolveService')
            ->with(DebugStep::class)
            ->willReturn($step);

        $this->runner->run($this->runner->buildConfigurationProcess('chocapic'), []);
    }

    /**
     * @throws \Exception
     */
    public function testRunWithMarkFailInFinalize()
    {
        $this->loggerRegistry
            ->expects($this->once())
            ->method('resolveService')
            ->with('monolog.logger.chocapic')
            ->willReturn(new NullLogger());

        $this->stepRegistry
            ->expects($this->at(0))
            ->method('resolveService')
            ->with(PredefinedDataStep::class)
            ->willReturn(new PredefinedDataStep());
        $this->stepRegistry
            ->expects($this->at(1))
            ->method('resolveService')
            ->with(IterateArrayStep::class)
            ->willReturn(new IterateArrayStep());
        // Item one
        $this->stepRegistry
            ->expects($this->at(2))
            ->method('resolveService')
            ->with(DebugStep::class)
            ->willReturn(new DebugStep());
        // Item two
        $this->stepRegistry
            ->expects($this->at(3))
            ->method('resolveService')
            ->with(DebugStep::class)
            ->willReturn(new DebugStep());

        $step = $this->createMock(DebugStep::class);
        $step
            ->expects($this->once())
            ->method('finalize')
            ->willReturnCallback(function(ProcessState $state) {
                $state->markFail();
            });

        // Finalize
        $this->stepRegistry
            ->expects($this->at(4))
            ->method('resolveService')
            ->with(DebugStep::class)
            ->willReturn($step);

        $this->runner->run($this->runner->buildConfigurationProcess('chocapic'), []);
    }


    /**
     * @throws \Exception
     */
    public function testRunFailWithException()
    {
        $step = $this->createPartialMock(PredefinedDataStep::class, ['execute']);
        $step
            ->expects($this->once())
            ->method('execute')
            ->willThrowException(new \Exception());

        $this->stepRegistry
            ->expects($this->at(0))
            ->method('resolveService')
            ->with(PredefinedDataStep::class)
            ->willReturn($step);

        $this->loggerRegistry
            ->expects($this->once())
            ->method('resolveService')
            ->with('monolog.logger.chocapic')
            ->willReturn($this->logger);

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('fail step');

        $this->runner->run($this->runner->buildConfigurationProcess('chocapic'), []);
    }


    public function testRunFailWithResultKO()
    {
        $this->loggerRegistry
            ->expects($this->once())
            ->method('resolveService')
            ->with('monolog.logger.chocapic')
            ->willReturn(new NullLogger());

        $step = $this->createPartialMock(PredefinedDataStep::class, ['execute']);
        $step
            ->expects($this->once())
            ->method('execute')
            ->willReturnCallback(function(ProcessState $state) {
                $state->markFail();
            });

        $this->stepRegistry
            ->expects($this->at(0))
            ->method('resolveService')
            ->with(PredefinedDataStep::class)
            ->willReturn($step);

        $this->runner->run($this->runner->buildConfigurationProcess('chocapic'), []);
    }
}