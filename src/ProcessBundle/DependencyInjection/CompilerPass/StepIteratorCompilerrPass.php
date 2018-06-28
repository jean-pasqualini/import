<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;

/**
 * @internal
 */
class StepIteratorCompilerrPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('darkilliant_process');

        foreach ($config['process'] as $processName => $process) {
            $config['process'][$processName]['steps'] = $this->buildTreeStep($container, $process['steps']);
        }

        $container->setParameter('darkilliant_process', $config);
    }

    private function buildTreeStep(ContainerBuilder $container, array $steps)
    {
        foreach ($steps as $stepId => $step) {
            $isStepIterable = in_array(
                IterableStepInterface::class,
                class_implements($container->findDefinition($step['service'])->getClass())
            );

            if ($isStepIterable) {
                $children = array_splice($steps, $stepId + 1);

                $steps[$stepId]['children'] = $this->buildTreeStep($container, $children);

                return $steps;
            }
        }

        return $steps;
    }
}
