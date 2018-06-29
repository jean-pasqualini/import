<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DeprecatedStepCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('darkilliant_process');

        foreach ($config['process'] as $processName => $process) {
            $deprecateds = [];
            foreach ($process['steps'] as $step) {
                if (class_exists($step['service']) && true === call_user_func_array([$step['service'], 'isDeprecated'], [])) {
                    $deprecateds[] = $step['service'];
                }
            }
            $config['process'][$processName]['deprecated'] = $deprecateds;
        }

        $container->setParameter('darkilliant_process', $config);
    }
}
