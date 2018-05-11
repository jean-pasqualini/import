<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Runner;

use Darkilliant\ProcessBundle\Configuration\ConfigurationProcess;
use Darkilliant\ProcessBundle\Logger\InMemoryLogger;
use Darkilliant\ProcessBundle\ProcessNotifier\ProgressBarProcessNotifier;
use Darkilliant\ProcessBundle\Registry\LoggerRegistry;
use Darkilliant\ProcessBundle\Registry\StepRegistry;
use Darkilliant\ProcessBundle\Resolver\OptionDynamicValueResolver;
use Darkilliant\ProcessBundle\Runner\StepDescripterRunner;
use Darkilliant\ProcessBundle\Step\DebugStep;
use Darkilliant\ProcessBundle\Step\IterateArrayStep;
use Darkilliant\ProcessBundle\Step\LaunchProcessStep;
use Darkilliant\ProcessBundle\Step\PredefinedDataStep;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class StepDescriptorRunnerTest extends TestCase
{
    /** @var StepDescripterRunner */
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
        $this->runner = new StepDescripterRunner(
            $this->loggerRegistry = $this->createMock(LoggerRegistry::class),
            $this->stepRegistry = $this->createMock(StepRegistry::class),
            $this->optionDynamicValueResolver = $this->createMock(OptionDynamicValueResolver::class),
            [
                'process' => [
                    'chocapic' => [
                        'logger' => 'monolog.logger.chocapic',
                        'steps' => [
                            [
                                'service' => LaunchProcessStep::class,
                                'options' => [
                                    'process' => 'lait'
                                ]
                            ],
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
                    'lait' => [
                        'logger' => 'monolog.logger.chocapic',
                        'steps' => [
                            [
                                'service' => PredefinedDataStep::class,
                                'options' => [
                                    'data' => [
                                        ['name' => 'lait'],
                                    ]
                                ]
                            ],
                        ]
                    ]
                ]
            ],
            $this->logger = $this->createMock(LoggerInterface::class),
            $this->progressBarNotifier = $this->createMock(ProgressBarProcessNotifier::class)
        );

        $this->optionDynamicValueResolverean
            ->expects($this->any())
            ->method('resolve')
            ->willReturnArgument(0);
    }

    public function testBuildConfigurationProcessWhenProcessNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('process unknow not found, available (chocapic, lait)');

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
            ->expects($this->exactly(2))
            ->method('resolveService')
            ->with(InMemoryLogger::class)
            ->willReturn(new NullLogger());

        $this->stepRegistry
            ->expects($this->at(0))
            ->method('resolveService')
            ->with(LaunchProcessStep::class)
            ->willReturn(new LaunchProcessStep());
        $this->stepRegistry
            ->expects($this->at(1))
            ->method('resolveService')
            ->with(PredefinedDataStep::class)
            ->willReturn(new PredefinedDataStep());
        $this->stepRegistry
            ->expects($this->at(2))
            ->method('resolveService')
            ->with(PredefinedDataStep::class)
            ->willReturn(new PredefinedDataStep());
        $this->stepRegistry
            ->expects($this->at(3))
            ->method('resolveService')
            ->with(IterateArrayStep::class)
            ->willReturn(new IterateArrayStep());
        $this->stepRegistry
            ->expects($this->at(4))
            ->method('resolveService')
            ->with(DebugStep::class)
            ->willReturn(new DebugStep());

        $this->runner->run($this->runner->buildConfigurationProcess('chocapic'), []);
    }
}