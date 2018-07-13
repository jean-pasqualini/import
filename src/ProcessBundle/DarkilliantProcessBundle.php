<?php

namespace Darkilliant\ProcessBundle;

use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\DeprecatedStepCompilerPass;
use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\FilterCompilerPass;
use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\ProcessNotifierCompilerPass;
use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\RegisterAliasPublicLoggerCompilerPass;
use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\RemoveUnconfigurableStepCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\StepIteratorCompilerrPass;

/**
 * @internal
 * Class DarkilliantProcessBundle
 *
 * @codeCoverageIgnore
 */
class DarkilliantProcessBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new RegisterAliasPublicLoggerCompilerPass());
        $containerBuilder->addCompilerPass(new DeprecatedStepCompilerPass());
        $containerBuilder->addCompilerPass(new StepIteratorCompilerrPass());
        $containerBuilder->addCompilerPass(new FilterCompilerPass());
        $containerBuilder->addCompilerPass(new RemoveUnconfigurableStepCompilerPass());
        $containerBuilder->addCompilerPass(new ProcessNotifierCompilerPass());
    }
}
