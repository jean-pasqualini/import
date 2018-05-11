<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/8/18
 * Time: 12:01 PM.
 */

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
