<?php

namespace Darkilliant\ProcessBundle\Runner;

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
use Darkilliant\ProcessBundle\ProcessNotifier\ProgressBarProcessNotifier;

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

    /** @var ProgressBarProcessNotifier */
    protected $notifier;

    /**
     * @internal
     */
    public function __construct(LoggerRegistry $loggerRegistry, StepRegistry $registry, OptionDynamicValueResolver $dynamicValueResolver, array $configuration, LoggerInterface $logger, ProgressBarProcessNotifier $notifier)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->dynamicValueResolver = $dynamicValueResolver;
        $this->registry = $registry;
        $this->loggerRegistry = $loggerRegistry;
        $this->notifier = $notifier;
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

        return ConfigurationProcess::create($this->configuration['process'][$processName]);
    }

    public function run(ConfigurationProcess $process, array $context = [])
    {
        $processState = new ProcessState(
            $context,
            $this->loggerRegistry->resolveService($process->getLogger()),
            $this
        );

        $this->runSteps($processState, $process->getSteps());
    }

    public function finalizeStep(ProcessState $processState, $step)
    {
        $processState->markSuccess();

        $this->registry->resolveService($step->getService())->finalize($processState);

        if (ProcessState::RESULT_OK !== $processState->getResult()) {
            return false;
        }

        return true;
    }

    public function runSteps(ProcessState $processState, array $steps)
    {
        foreach ($steps as $step) {
            try {
                if (!$this->runStep($processState, $step)) {
                    return false;
                }
            } catch (\Throwable $exception) {
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

    protected function runStep(ProcessState $processState, ConfigurationStep $step): int
    {
        $processState->markSuccess();

        /**
         * @var ConfigurationStep
         */
        $service = $this->registry->resolveService($step->getService());

        $processState = $this->configureOptions($service, $step, $processState);
        $options = $processState->getOptions();

        $service->execute($processState);

        if (ProcessState::RESULT_OK !== $processState->getResult()) {
            return false;
        }
        if ($service instanceof IterableStepInterface) {
            $this->notifier->onStartProcess($processState, $service);
            $iterator = $processState->getIterator();

            while ($service->valid($processState)) {
                $service->next($processState);
                $this->notifier->onUpdateProcess($processState, $service);

                if ($this->runSteps($processState, $step->getChildren())) {
                    $processState->getLogger()->info('successful', $processState->getRawContext());
                }

                $processState->setIterator($iterator);
                $processState->setOptions($options);
            }
            $this->notifier->onEndProcess($processState);

            $this->finalizeSteps($processState, $step->getChildren());
        }

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
                $processState->getLogger()->error('fail step', array_merge([
                    'message' => $exception->getMessage(),
                    'step' => $step->getService(),
                ], $processState->getRawContext()));

                return false;
            }
        }

        return true;
    }
}
