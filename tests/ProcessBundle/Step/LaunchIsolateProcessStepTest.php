<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\LaunchIsolateProcessStep;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class LaunchIsolateProcessStepTest extends TestCase
{
    /** @var LaunchIsolateProcessStep|MockObject */
    private $step;

    /** @var ProcessBuilder|MockObject */
    private $processBuilder;

    /** @var PhpExecutableFinder|MockObject */
    private $phpExecutableFinder;

    /** @var BufferedOutput */
    private $output;

    public static function setUpBeforeClass()
    {
        ClockMock::register(LaunchIsolateProcessStep::class);
        ClockMock::withClockMock(true);
    }

    public function setUp()
    {
        $this->output = new BufferedOutput();
        $this->processBuilder = $this->createMock(ProcessBuilder::class);
        $this->phpExecutableFinder = $this->createMock(PhpExecutableFinder::class);
        $this->step = $this->getMockBuilder(LaunchIsolateProcessStep::class)
            ->setConstructorArgs(['prod'])
            ->setMethods(['getProcessBuilder', 'getPhpExecutableFinder'])
            ->getMock();
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    public function provideExecuteWithMultipleVerbosity()
    {
        yield 'normal' => [OutputInterface::VERBOSITY_NORMAL, [
            '/path/php',
            '/project/bin/console',
            'process:run',
            '--env=prod',
            '--input-from-stdin',
            '--force-color',
            '--context context_key=context_value',
            '--',
            'rocket',
        ]];

        yield 'normal with dry run' => [OutputInterface::VERBOSITY_NORMAL, [
            '/path/php',
            '/project/bin/console',
            'process:run',
            '--env=prod',
            '--input-from-stdin',
            '--force-color',
            '--context context_key=context_value',
            '--dry-run',
            '--',
            'rocket',
        ], true];
        yield 'verbose' => [OutputInterface::VERBOSITY_VERBOSE, [
            '/path/php',
            '/project/bin/console',
            'process:run',
            '--env=prod',
            '--input-from-stdin',
            '--force-color',
            '-v',
            '--context context_key=context_value',
            '--',
            'rocket',
        ]];
        yield 'debug' => [OutputInterface::VERBOSITY_DEBUG, [
            '/path/php',
            '/project/bin/console',
            'process:run',
            '--env=prod',
            '--input-from-stdin',
            '--force-color',
            '-vvv',
            '--context context_key=context_value',
            '--',
            'rocket',
        ]];
        yield 'quiet' => [OutputInterface::VERBOSITY_QUIET, [
            '/path/php',
            '/project/bin/console',
            'process:run',
            '--env=prod',
            '--input-from-stdin',
            '--force-color',
            '-q',
            '--context context_key=context_value',
            '--',
            'rocket',
        ]];
        yield 'very verbose' => [OutputInterface::VERBOSITY_VERY_VERBOSE, [
            '/path/php',
            '/project/bin/console',
            'process:run',
            '--env=prod',
            '--input-from-stdin',
            '--force-color',
            '-vv',
            '--context context_key=context_value',
            '--',
            'rocket',
        ]];
    }


    /**
     * @dataProvider provideExecuteWithMultipleVerbosity
     */
    public function testExecuteWithOutput($verbosity, $expectedParameter, $isDryRun = false)
    {
        $this->output->setVerbosity($verbosity);
        $this->step->onCommand(new ConsoleCommandEvent(null, new ArgvInput([]), $this->output));

        $this->runTestExecute($expectedParameter, $isDryRun);
    }

    public function testExecuteWithoutOutput()
    {
        $this->runTestExecute([
            '/path/php',
            '/project/bin/console',
            'process:run',
            '--env=prod',
            '--input-from-stdin',
            '--force-color',
            '-vv',
            '--context context_key=context_value',
            '--',
            'rocket',
        ]);
    }

    private function runTestExecute($expectedParameter, $isDryRun = false)
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setDryRun($isDryRun);
        $state->setOptions([
            'process_name' => 'rocket',
            'max_concurency' => 1,
            'context' => ['context_key' => 'context_value'],
            'data' => [],
            'timeout' => 10000,
            'bin_console_path' => '/project/bin/console'
        ]);

        $process = $this->createMock(Process::class);

        $this->step
            ->expects($this->once())
            ->method('getProcessBuilder')
            ->willReturn($this->processBuilder);
        $this->step
            ->expects($this->once())
            ->method('getPhpExecutableFinder')
            ->willReturn($this->phpExecutableFinder);

        $this->phpExecutableFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn('/path/php');

        $this->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with($expectedParameter);
        $this->processBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(10000);
        $this->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('start');

        $process
            ->expects($this->exactly(2))
            ->method('isStarted')
            ->willReturnOnConsecutiveCalls(false, true);

        $process
            ->expects($this->never())
            ->method('wait');

        $this->step->execute($state);
        $this->step->finalize($state);
    }
}
