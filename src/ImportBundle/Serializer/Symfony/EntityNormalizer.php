<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Serializer\Symfony;

use Darkilliant\ImportBundle\Resolver\EntityResolver;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;

/**
 * @internal
 * Entity normalizer
 */
class EntityNormalizer extends ObjectNormalizer
{
    /** @var array */
    protected $config;

    /** @var EntityResolver */
    private $resolver;

    public function __construct(
        array $config,
        EntityResolver $resolver,
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        PropertyTypeExtractorInterface $propertyTypeExtractor = null
    ) {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
        // Entity manager
        $this->resolver = $resolver;
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
        $entity = $this->resolver->resolve($class, $data, $context['entity_resolver'][$class] ?? null);

        $entity = $entity ?? new $class();

        $context['object_to_populate'] = $entity;
        $data['_resolved'] = true;

        return $this->serializer->denormalize($data, $class, $format, $context);
    }
}
