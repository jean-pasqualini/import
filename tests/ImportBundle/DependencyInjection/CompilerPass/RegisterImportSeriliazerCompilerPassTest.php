<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\DependencyInjection\CompilerPass;


use Darkilliant\ImportBundle\DependencyInjection\CompilerPass\RegisterImportSerializerCompilerPass;
use JMS\Serializer\Serializer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterImportSeriliazerCompilerPassTest extends TestCase
{
    /** @var RegisterImportSerializerCompilerPass */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new RegisterImportSerializerCompilerPass();
    }

    public function testNotRegisterWhenNotExistsJmsSerializer()
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);

        $containerBuilder
            ->expects($this->once())
            ->method('hasAlias')
            ->with('jms_serializer')
            ->willReturn(false);

        $containerBuilder
            ->expects($this->never())
            ->method('setDefinition');

        $this->compiler->process($containerBuilder);
    }

    public function testRegisterWhenExistsJmsSerializer()
    {
        $containerBuilder = new ContainerBuilder();

        $jmsDefinition = $containerBuilder->register('jms_serializer.serializer', Serializer::class);
        $containerBuilder->setAlias('jms_serializer', 'jms_serializer.serializer');

        $this->compiler->process($containerBuilder);

        $this->assertEquals($jmsDefinition, $containerBuilder->findDefinition('jms_serializer'));

        $importJmsDefinition = $containerBuilder->findDefinition('import_jms_serializer');

        $this->assertNotEquals($importJmsDefinition, $jmsDefinition);
    }
}