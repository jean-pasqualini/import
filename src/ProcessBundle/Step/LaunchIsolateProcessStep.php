<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class LaunchIsolateProcessStep extends AbstractConfigurableStep implements EventSubscriberInterface
{
    private $processCollection = [];
    private $verbosity;
    private $environment;

    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => ['onCommand', 255],
        ];
    }

    public function onCommand(ConsoleEvent $event)
    {
        $this->verbosity = $event->getOutput()->getVerbosity();
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['process_name', 'max_concurency', 'context', 'data', 'timeout', 'bin_console_path']);
        $resolver->setDefault('context', []);
        $resolver->setDefault('data', []);
        $resolver->setDefault('max_concurency', 1);
        $resolver->setDefault('timeout', 60 * 20);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $processName = $state->getOptions()['process_name'];
        $maxConcurency = $state->getOptions()['max_concurency'];
        $context = $state->getOptions()['context'];
        $data = $state->getOptions()['data'];

        $processBuilder = $this->getProcessBuilder();
        $phpFinder = $this->getPhpExecutableFinder();

        $arguments = [
            $phpFinder->find(),
            $state->getOptions()['bin_console_path'],
            'process:run',
            '--env=prod',
            '--input-from-stdin',
            '--force-color',
        ];

        $verbosityParameter = $this->getVerbosityParameter();
        if ($verbosityParameter) {
            $arguments[] = $verbosityParameter;
        }

        foreach ($context as $key => $value) {
            $arguments[] = sprintf('--context %s=%s', $key, $value);
        }

        if ($state->isDryRun()) {
            $arguments[] = '--dry-run';
        }

        $arguments[] = '--';
        $arguments[] = $processName;
        $processBuilder->setArguments($arguments);

        $processBuilder->setInput(json_encode($data));
        $processBuilder->setTimeout($state->getOptions()['timeout']);
        $processBuilder->disableOutput();
        $process = $processBuilder->getProcess();

        $this->processCollection[] = $process;

        $process->start(function ($type, $output) {
            echo $output;
        });

        if (count($this->processCollection) >= $maxConcurency) {
            $this->wait();
        }
    }

    public function finalize(ProcessState $state)
    {
        $this->wait();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getProcessBuilder(): ProcessBuilder
    {
        return new ProcessBuilder();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getPhpExecutableFinder(): PhpExecutableFinder
    {
        return new PhpExecutableFinder();
    }

    protected function getVerbosityParameter()
    {
        if (!$this->verbosity) {
            return '-vv';
        }
        switch ($this->verbosity) {
            case OutputInterface::VERBOSITY_QUIET:
                return '-q';
            case OutputInterface::VERBOSITY_VERBOSE:
                return '-v';
            case OutputInterface::VERBOSITY_VERY_VERBOSE:
                return '-vv';
            case OutputInterface::VERBOSITY_DEBUG:
                return '-vvv';
        }

        return null;
    }

    private function wait()
    {
        foreach ($this->processCollection as $process) {
            /* @var Process $process */
            $process->wait();
        }
        $this->processCollection = [];
    }
}
