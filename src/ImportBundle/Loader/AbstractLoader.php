<?php

namespace Darkilliant\ImportBundle\Loader;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class AbstractLoader.
 *
 * @internal
 */
abstract class AbstractLoader
{
    /** @var PropertyAccessorInterface */
    protected $accessor;

    public function __construct(PropertyAccessorInterface $accessor)
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
