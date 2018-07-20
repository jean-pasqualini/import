<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Registry;

use Darkilliant\ImportBundle\WhereBuilder\WhereBuilderInterface;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
class EntityResolverWhereBuilderRegistry
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolveService(string $className): WhereBuilderInterface
    {
        return $this->container->get($className);
    }
}
