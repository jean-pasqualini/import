<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Registry;

use Psr\Container\ContainerInterface;

/**
 * @internal
 */
class StepRegistry
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolveService(string $className)
    {
        return $this->container->get($className);
    }
}
