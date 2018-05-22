<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Serializer\JMS;

use Doctrine\Common\Persistence\ManagerRegistry;
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
    private $managerRegistry;
    private $fallbackConstructor;

    /**
     * Constructor.
     *
     * @param ManagerRegistry            $managerRegistry     Manager registry
     * @param ObjectConstructorInterface $fallbackConstructor Fallback object constructor
     */
    public function __construct(ManagerRegistry $managerRegistry, ObjectConstructorInterface $fallbackConstructor, array $config)
    {
        $this->managerRegistry = $managerRegistry;
        $this->fallbackConstructor = $fallbackConstructor;
        $this->config = $config;
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

        $class = $metadata->name;
        $config = $this->config[$class];

        // Locate possible ObjectManager
        $objectManager = $this->managerRegistry->getManagerForClass($class);

        foreach ($config as $key => $fieldName) {
            $dataFieldName = (is_integer($key)) ? $fieldName : $key;
            if (!empty($data[$dataFieldName])) {
                $where[$fieldName] = $data[$dataFieldName];
            }
        }

        if (empty($where)) {
            return new $class();
        }

        $entity = $objectManager->getRepository($class)->findOneBy($where);
        $entity = $entity ?? new $class();

        return $entity;
    }
}
