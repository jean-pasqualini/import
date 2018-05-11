<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\TargetResolver;

/**
 * @internal
 */
class ArrayTargetResolver
{
    /** @var DoctrineTargetResolver */
    private $resolver;

    public function __construct(DoctrineTargetResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param array $data
     *
     * @throws \Exception
     *
     * @return array
     */
    public function resolve(array $data, array $config)
    {
        foreach ($config as $propertyName => $propertyConfig) {
            $data[$propertyName] = $this->resolver->resolve($propertyConfig);
        }

        return $data;
    }
}
