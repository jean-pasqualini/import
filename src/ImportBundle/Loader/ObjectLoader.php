<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Loader;

/**
 * @internal
 */
class ObjectLoader extends AbstractLoader
{
    public function load($object, array $data, array $options)
    {
        return $this->loadObject($object, $data, $options['mapping'], $options['mapping_relation']);
    }
}
