<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Serializer\JMS;

use Darkilliant\ImportBundle\Resolver\EntityResolver;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Construction\ObjectConstructorInterface;

/**
 * @internal
 * Doctrine object constructor for new (or existing) objects during deserialization
 */
class DoctrineObjectConstructor implements ObjectConstructorInterface
{
    /** @var array */
    protected $config;
    private $fallbackConstructor;

    /** @var EntityResolver */
    private $resolver;

    public function __construct(ObjectConstructorInterface $fallbackConstructor, EntityResolver $resolver, array $config)
    {
        $this->fallbackConstructor = $fallbackConstructor;
        $this->config = $config;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        if (!isset($this->config[$metadata->name])) {
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Deprecated ignored for compatibilty with lower version of jms serializer
        $class = $metadata->name;
        $entity = $this->resolver->resolve($class, $data, /* @scrutinizer ignore-deprecated */ $context->attributes->all()['entity_resolver'][$class] ?? null);

        return (null === $entity) ? new $class() : $entity;
    }
}
