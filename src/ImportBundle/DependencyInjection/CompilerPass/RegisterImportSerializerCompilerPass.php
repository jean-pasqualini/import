<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\DependencyInjection\CompilerPass;

use Darkilliant\ImportBundle\Serializer\JMS\DoctrineObjectConstructor;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
class RegisterImportSerializerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasAlias('jms_serializer')) {
            $importJmsDefinition = new ChildDefinition('jms_serializer');
            $importJmsDefinition->setArgument(
                '$objectConstructor',
                new Reference(DoctrineObjectConstructor::class)
            );
            $importJmsDefinition->setPublic(true);

            $container->setDefinition('import_jms_serializer', $importJmsDefinition);

            $container->setAlias(Serializer::class, 'jms_serializer.serializer');
        }
    }
}
