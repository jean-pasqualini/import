<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\DependencyInjection\CompilerPass;

use Darkilliant\ProcessBundle\Filter\ValidatorFilter;
use Darkilliant\ProcessBundle\Step\ValidateObjectStep;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 * @codeCoverageIgnore
 */
class RemoveUnconfigurableStepCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Service depends of validator
        if (!$container->hasDefinition('validator')) {
            $container->removeDefinition(ValidateObjectStep::class);
            $container->removeDefinition(ValidatorFilter::class);
        }
    }
}
