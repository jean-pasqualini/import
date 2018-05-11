<?php

namespace Darkilliant\ImportBundle\Loader;

use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class AbstractLoader.
 */
abstract class AbstractLoader
{
    /** @var $accessor */
    protected $accessor;

    public function __construct(PropertyAccessor $accessor)
    {
        $this->accessor = $accessor;
    }

    protected function loadObject($object, array $data, array $mapping, array $mappingRelation = [])
    {
        foreach ($mapping as $objectPath => $arrayPath) {
            if (!is_array($arrayPath)) {
                $propertyAccesorPath = substr($arrayPath, 1);
                if (false === strpos($arrayPath, '@')) {
                    $this->accessor->setValue($object, $objectPath, $arrayPath);
                } elseif ($this->accessor->isReadable($data, $propertyAccesorPath)) {
                    $this->accessor->setValue($object, $objectPath, $this->accessor->getValue($data, $propertyAccesorPath));
                }
            }

            if (isset($mappingRelation[$objectPath]) && array_key_exists($objectPath, $data)) {
                $classRelation = $mappingRelation[$objectPath];
                $this->accessor->setValue(
                    $object,
                    $objectPath,
                    $this->loadObject(new $classRelation(), $data[$objectPath], $arrayPath)
                );
            }
        }

        return $object;
    }
}
