<?php

declare(strict_types=1);

namespace Tests\Darkilliant\MqProcessBundle\DependencyInjection;

use Darkilliant\MqProcessBundle\DependencyInjection\DarkilliantMqProcessExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DarkilliantMqProcessExtensionTest extends TestCase
{
    /** @var DarkilliantMqProcessExtension */
    private $extension;

    public function setUp()
    {
        $this->extension = new DarkilliantMqProcessExtension();
    }

    public function testLoad()
    {
        $container = new ContainerBuilder();
        $this->extension->load(
            [
                [
                    'client' => [
                        'host' => '127.0.0.1',
                        'port' => 3306,
                        'user' => 'root',
                        'password' => 'root',
                        'vhost' => 'rabbitmq'
                    ]
                ]
            ],
            $container
        );

        $definition = $container->getDefinition('darkilliant_mqprocess_connection');

        $this->assertEquals('127.0.0.1', $definition->getArgument('$host'));
        $this->assertEquals(3306, $definition->getArgument('$port'));
        $this->assertEquals('root', $definition->getArgument('$user'));
        $this->assertEquals('root', $definition->getArgument('$password'));
        $this->assertEquals('rabbitmq', $definition->getArgument('$vhost'));
    }
}