<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/7/18
 * Time: 8:20 PM.
 */

namespace Darkilliant\ImportBundle\TargetResolver;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @internal
 */
class DoctrineTargetResolver
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function resolve(array $config)
    {
        $repository = $this->em->getRepository($config['entityClass']);
        $call = $this->factoryCall($config['strategy']['name'], $config['entityClass'], $config['strategy']['options']);

        // When not strategy found
        if (null === $call) {
            throw new \Exception('factory fail');
        }

        // When method of repository or entityManager with params
        $entity = call_user_func_array(
            [
                ('repository' === $call['in'])
                    ? $repository
                    : $this->em, $call['method'], ],
            $call['params']
        );

        // When not entity already exist in database
        if (null === $entity) {
            return ($config['create']) ? new $config['entityClass']() : null;
        }

        return $entity;
    }

    private function factoryCall(string $strategy, string $entityClass, array $options)
    {
        switch ($strategy) {
            case 'findOneBy':
                return $this->factoryFindOneBy($options);
            break;
            case 'find':
                return $this->factoryFind($entityClass, $options);
            break;
        }

        return null;
    }

    private function factoryFindOneBy(array $options)
    {
        return [
            'in' => 'repository',
            'method' => 'findOneBy',
            'params' => [$options],
        ];
    }

    private function factoryFind(string $entityClass, array $options)
    {
        return [
            'in' => 'entity_manager',
            'method' => 'getReference',
            'params' => [$entityClass, $options[0]],
        ];
    }
}
