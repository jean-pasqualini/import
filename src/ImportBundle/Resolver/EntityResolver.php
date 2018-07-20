<?php

namespace Darkilliant\ImportBundle\Resolver;

use Darkilliant\ImportBundle\Registry\EntityResolverWhereBuilderRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

class EntityResolver
{
    const STRATEGY_WHERE = 'where';
    /** @var array */
    private $config;

    /** @var ManagerRegistry */
    private $registry;

    private $cache = [];

    /** @var EntityResolverWhereBuilderRegistry */
    private $whereBuilderRegistry;

    /**
     * @var array
     */
    private $cacheable;

    public function __construct(ManagerRegistry $registry, EntityResolverWhereBuilderRegistry $whereBuilderRegistry, array $config, array $cacheable)
    {
        $this->registry = $registry;
        $this->config = $config;
        $this->cacheable = $cacheable;
        $this->whereBuilderRegistry = $whereBuilderRegistry;
    }

    public function resolve($class, array $data, array $config = null)
    {
        if (null === $config) {
            $config = $this->config[$class] ?? [];
        }

        // Legacy format (remove for 1.1)
        if (isset($config[0])) {
            $config = [
                'strategy' => self::STRATEGY_WHERE,
                'options' => [
                    'service' => null,
                    'fields' => $config,
                ],
            ];
        }

        if (empty($config)) {
            return null;
        }

        if (self::STRATEGY_WHERE === $config['strategy']) {
            // Use custom where
            if ($config['options']['service']) {
                $where = $this->whereBuilderRegistry->resolveService(
                    $config['options']['service']
                )->buildWhere($class, $config['options'], $data);
            } else {
                $where = $this->buildWhere($config['options']['fields'], $data);
            }

            if (empty($where)) {
                return null;
            }

            if (!$this->isCacheable($class)) {
                return $this->fetchFromRepository($class, $where);
            }

            if (!$this->isCacheBuilded($class)) {
                $this->cache[$class] = $this->buildCache($class, $where);
            }

            return $this->fetchFromCache($class, $where);
        }

        throw new \Exception(sprintf(
            'unknow strategy %s, resolve entity %s',
            $config['strategy'],
            $class
        ));
    }

    public function clear($class = null)
    {
        if (null === $class) {
            $this->cache = [];

            return;
        }

        if (isset($this->cache[$class])) {
            $this->cache[$class] = [];

            return;
        }

        return;
    }

    private function fetchFromCache(string $class, array $where)
    {
        $searchCompostiteKey = implode('_', array_values($where));

        $entityId = $this->cache[$class][$searchCompostiteKey] ?? null;

        /** @var EntityManager $objectManager */
        $objectManager = $this->registry->getManagerForClass($class);

        if (null !== $entityId) {
            $entity = $objectManager
                ->getUnitOfWork()
                ->createEntity($class, ['id' => $entityId]);
            $objectManager
                ->getUnitOfWork()
                ->setOriginalEntityData(
                    $entity,
                    $this->getFakeOriginalData($objectManager, $entity)
                );

            return $entity;
        }

        return null;
    }

    private function buildWhere(array $config, array $data): array
    {
        $where = [];
        foreach ($config as $key => $fieldName) {
            $dataFieldName = (is_integer($key)) ? $fieldName : $key;
            if (!empty($data[$dataFieldName])) {
                $where[$fieldName] = $data[$dataFieldName];
            }
        }

        return $where;
    }

    private function isCacheable($class)
    {
        return $this->cacheable[$class] ?? false;
    }

    private function isCacheBuilded($class)
    {
        return isset($this->cache[$class]);
    }

    private function buildCache($class, $where): array
    {
        /** @var EntityManager $objectManager */
        $objectManager = $this->registry->getManagerForClass($class);

        $selectFields = [];
        foreach ($where as $fieldName => $fieldValue) {
            $selectFields[] = sprintf('o.%s', $fieldName);
        }

        $qb = $objectManager->createQueryBuilder()
            ->select('o.id, '.implode(', ', $selectFields))
            ->from($class, 'o')
            ->indexBy('o', 'o.id');

        $result = $qb->getQuery()->getArrayResult();

        $cache = [];
        foreach ($result as $resultItem) {
            $compositeValues = $resultItem;
            if (!isset($where['id'])) {
                unset($compositeValues['id']);
            }
            $compositeKey = implode('_', array_values($compositeValues));

            $cache[$compositeKey] = $resultItem['id'];
        }

        return $cache;
    }

    private function fetchFromRepository($class, $where)
    {
        $objectManager = $this->registry->getManagerForClass($class);

        return $objectManager->getRepository($class)->findOneBy($where);
    }

    private function getFakeOriginalData(EntityManager $em, $entity)
    {
        $class = $em->getClassMetadata(get_class($entity));

        $field = [];

        foreach ($class->reflFields as $name => $refProp) {
            $field[$name] = null;
        }

        return $field;
    }
}
