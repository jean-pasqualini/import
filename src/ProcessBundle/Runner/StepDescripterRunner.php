<?php

namespace Darkilliant\ProcessBundle\Runner;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\Configuration\ConfigurationProcess;
use Darkilliant\ProcessBundle\Configuration\ConfigurationStep;
use Darkilliant\ProcessBundle\Logger\InMemoryLogger;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;
use Darkilliant\ProcessBundle\Step\LaunchProcessStep;
use Darkilliant\ProcessBundle\Step\StepInterface;

class StepDescripterRunner extends StepRunner
{
    /**
     * @param string $processName
     *
     * @throws \Exception
     *
     * @return ConfigurationProcess
     */
    public function buildConfigurationProcess(string $processName, string $logger = null): ConfigurationProcess
    {
        return parent::buildConfigurationProcess($processName, InMemoryLogger::class);
    }

    protected function configureOptions(StepInterface $service, ConfigurationStep $step, ProcessState $processState): ProcessState
    {
        return $processState->setOptions(
            $service->configureOptionResolver(new OptionsResolver())->resolve($step->getOptions())
        );
    }

    protected function runStep(ProcessState $processState, ConfigurationStep $step): int
    {
        if (LaunchProcessStep::class === $step->getService()) {
            parent::runStep($processState, $step);

            return ProcessState::RESULT_OK;
        }

        /**
         * @var ConfigurationStep
         */
        $service = $this->registry->resolveService($step->getService());

        $this->configureOptions($service, $step, $processState);
        $service->describe($processState);

        if ($service instanceof IterableStepInterface) {
            $this->runSteps($processState, $step->getChildren());
        }

        return ProcessState::RESULT_OK;
    }
}
