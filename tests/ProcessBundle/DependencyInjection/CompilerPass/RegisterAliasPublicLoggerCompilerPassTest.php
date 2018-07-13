<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\DependencyInjection\CompilerPass;

use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\RegisterAliasPublicLoggerCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterAliasPublicLoggerCompilerPassTest extends TestCase
{
    /** @var RegisterAliasPublicLoggerCompilerPass */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new RegisterAliasPublicLoggerCompilerPass();
    }

    public function testProcess()
    {
        $container = new ContainerBuilder();

        $container->register('logger', Logger::class);

        $container->setParameter(
            'darkilliant_process',
            [
                'process' => [
                    'demo' => [
                        'logger' => 'logger',
                    ],
                ],
            ]
        );

        $this->compiler->process($container);

        $this->assertEquals('logger', (string) $container->getAlias('darkilliant_process_logger_demo'));
    }
}