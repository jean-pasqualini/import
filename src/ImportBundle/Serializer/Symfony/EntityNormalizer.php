<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Serializer\Symfony;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * @internal
 * Entity normalizer
 */
class EntityNormalizer extends ObjectNormalizer
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var Serializer
     */
    protected $serializer;

    /** @var array */
    protected $config;

    public function __construct(
        array $config,
        ManagerRegistry $managerRegistry,
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        PropertyTypeExtractorInterface $propertyTypeExtractor = null
    ) {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
        // Entity manager
        $this->managerRegistry = $managerRegistry;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_array($data) && !isset($data['_resolved']) && isset($this->config[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $config = $this->config[$class] ?? [];

        $where = [];

        if (!$config) {
            return null;
        }
        foreach ($config as $key => $fieldName) {
            $dataFieldName = (is_integer($key)) ? $fieldName : $key;
            if (!empty($data[$dataFieldName])) {
                $where[$fieldName] = $data[$dataFieldName];
            }
        }

        if (empty($where)) {
            return new $class();
        }

        $entity = $this->managerRegistry->getManagerForClass($class)->getRepository($class)->findOneBy($where);
        $entity = $entity ?? new $class();

        $context['object_to_populate'] = $entity;
        $data['_resolved'] = true;

        return $this->serializer->denormalize($data, $class, $format, $context);
    }
}
