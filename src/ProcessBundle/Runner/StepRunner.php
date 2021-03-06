<?php

namespace Darkilliant\ProcessBundle\Runner;

use Darkilliant\ProcessBundle\ProcessNotifier\ChainProcessNotifier;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\Configuration\ConfigurationProcess;
use Darkilliant\ProcessBundle\Configuration\ConfigurationStep;
use Darkilliant\ProcessBundle\Registry\LoggerRegistry;
use Darkilliant\ProcessBundle\Registry\StepRegistry;
use Darkilliant\ProcessBundle\Resolver\OptionDynamicValueResolver;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;
use Darkilliant\ProcessBundle\Step\StepInterface;

class StepRunner
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var StepRegistry */
    protected $registry;

    /** @var LoggerRegistry */
    protected $loggerRegistry;

    /** @var OptionDynamicValueResolver */
    protected $dynamicValueResolver;

    /** @var array */
    protected $configuration;

    /** @var ChainProcessNotifier */
    protected $notifier;

    private $shouldStop = false;

    private $pcntlSupported = false;

    private $countStopScheduled = 0;

    /**
     * @internal
     */
    public function __construct(LoggerRegistry $loggerRegistry, StepRegistry $registry, OptionDynamicValueResolver $dynamicValueResolver, array $configuration, LoggerInterface $logger, ChainProcessNotifier $notifier)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->dynamicValueResolver = $dynamicValueResolver;
        $this->registry = $registry;
        $this->loggerRegistry = $loggerRegistry;
        $this->notifier = $notifier;
        $this->pcntlSupported = 'cli' === PHP_SAPI && extension_loaded('pcntl');

        if ($this->pcntlSupported) {
            pcntl_signal(SIGINT, [$this, 'stop']);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function stop()
    {
        $this->logger->error('step runner stop scheduled');
        $this->shouldStop = true;
        ++$this->countStopScheduled;

        if ($this->countStopScheduled >= 3) {
            $this->logger->error('step runner stop forced');
            exit(1);
        }
    }

    public function setNotifier($notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * @param string $processName
     *
     * @throws \Exception
     *
     * @return ConfigurationProcess
     */
    public function buildConfigurationProcess(string $processName, string $logger = null): ConfigurationProcess
    {
        if (empty($this->configuration['process'][$processName])) {
            throw new \Exception(sprintf(
                'process %s not found, available (%s)',
                $processName,
                implode(', ', array_keys($this->configuration['process']))
            ));
        }

        if (null !== $logger) {
            $this->configuration['process'][$processName]['logger'] = $logger;
        }

        return ConfigurationProcess::create($processName, $this->configuration['process'][$processName]);
    }

    public function run(ConfigurationProcess $process, array $context = [], $data = [], $dryRun = false): bool
    {
        $processState = new ProcessState(
            $context,
            $this->loggerRegistry->resolveService($process->getLogger()),
            $this
        );
        $processState->setData($data);
        $processState->setDryRun($dryRun);
        $processState->setName($process->getName());

        if ($process->getDeprecated()) {
            $processState->warning('DEPRECATED STEPS USED', ['deprecated' => $process->getDeprecated()]);
        }

        $this->notifier->onStartRunner($processState);
        $isSucessFull = $this->runSteps($processState, $process->getSteps());
        $this->notifier->onEndRunner($processState, $isSucessFull);

        return $isSucessFull;
    }

    public function finalizeStep(ProcessState $processState, ConfigurationStep $step)
    {
        $processState->markSuccess();

        $service = $this->registry->resolveService($step->getService());
        $this->configureOptionsWithoutResolve($service, $step, $processState);
        $service->finalize($processState);

        if (ProcessState::RESULT_OK !== $processState->getResult()) {
            return false;
        }

        return true;
    }

    public function runSteps(ProcessState $processState, array $steps): bool
    {
        $processState->setContext('current_error', null);

        foreach ($steps as $step) {
            try {
                if (!$this->runStep($processState, $step)) {
                    return false;
                }
            } catch (\Throwable $exception) {
                $processState->setContext('current_error', $exception, false);
                $processState->getLogger()->error('fail step', array_merge([
                    'message' => $exception->getMessage(),
                    'step' => $step->getService(),
                ], $processState->getRawContext()));

                return false;
            }
        }

        return true;
    }

    protected function configureOptions(StepInterface $service, ConfigurationStep $step, ProcessState $processState): ProcessState
    {
        return $processState->setOptions(
            $this->dynamicValueResolver->resolve(
                $service->configureOptionResolver(new OptionsResolver())->resolve($step->getOptions()),
                [
                    'data' => $processState->getData(),
                    'context' => $processState->getRawContext(),
                ]
            )
        );
    }

    protected function configureOptionsWithoutResolve(StepInterface $service, ConfigurationStep $step, ProcessState $processState): ProcessState
    {
        return $processState->setOptions(
            $service->configureOptionResolver(new OptionsResolver())->resolve($step->getOptions())
        );
    }

    protected function runStep(ProcessState $processState, ConfigurationStep $step): int
    {
        $processState->markSuccess();

        if (!$step->isEnabled()) {
            return true;
        }

        /**
         * @var ConfigurationStep
         */
        $service = $this->registry->resolveService($step->getService());

        $processState = $this->configureOptions($service, $step, $processState);
        $options = $processState->getOptions();

        $this->notifier->onStartProcess($processState, $service);

        $service->execute($processState);

        $this->notifier->onExecutedProcess($processState, $service);

        if (ProcessState::RESULT_OK !== $processState->getResult()) {
            return false;
        }
        if ($service instanceof IterableStepInterface) {
            $this->notifier->onStartIterableProcess($processState, $service);

            $iterator = $processState->getIterator();
            $loopContext = $processState->getLoopContext();

            $count = $service->count($processState);

            while ($service->valid($processState)) {
                if ($this->pcntlSupported) {
                    // check ctrl+c (SIGINT)
                    pcntl_signal_dispatch();
                }

                $currentIndex = $service->getProgress($processState);

                $service->next($processState);
                $this->notifier->onUpdateIterableProcess($processState, $service);

                if ($this->shouldStop || ProcessState::RESULT_BREAK === $processState->getResult()) {
                    $processState->noLoop();
                    $this->finalizeSteps($processState, $step->getChildren());
                    $this->notifier->onEndProcess($processState, $service);

                    return true;
                }

                // Add metadata information of the current iteration of loop
                $processState->loop($currentIndex, $count, !$service->valid($processState));

                $isSuccessful = $this->runSteps($processState, $step->getChildren());
                $processState->setIterator($iterator);
                $processState->setOptions($options);
                $processState->setLoopContext($loopContext);

                if ($isSuccessful) {
                    $service->onSuccessLoop($processState);
                    $this->notifier->onSuccessLoop($processState, $service);
                    $processState->info('successful');
                } else {
                    $service->onFailedLoop($processState);
                    $this->notifier->onFailedLoop($processState, $service);
                }
            }
            $processState->noLoop();

            $this->finalizeSteps($processState, $step->getChildren());
        }

        $this->notifier->onEndProcess($processState, $service);

        return true;
    }

    private function finalizeSteps(ProcessState $processState, array $steps)
    {
        foreach ($steps as $step) {
            try {
                if (!$this->finalizeStep($processState, $step)) {
                    return false;
                }
            } catch (\Exception $exception) {
                $processState->error('fail step', [
                    'message' => $exception->getMessage(),
                    'step' => $step->getService(),
                ]);

                return false;
            }
        }

        return true;
    }
}
