<?php

namespace Darkilliant\ProcessBundle;

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
        $containerBuilder->addCompilerPass(new StepIteratorCompilerrPass());
    }
}
