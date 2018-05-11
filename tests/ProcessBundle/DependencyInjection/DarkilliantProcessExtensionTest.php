<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\DependencyInjection;

use Darkilliant\ProcessBundle\DependencyInjection\DarkilliantProcessExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DarkilliantProcessExtensionTest extends TestCase
{
    /** @var DarkilliantProcessExtension */
    private $extension;

    public function setUp()
    {
        $this->extension = new DarkilliantProcessExtension();
    }

    public function testProces()
    {
        $container = new ContainerBuilder();
        $this->extension->load([
            [
                'process' => [
                    'red' => [
                        'logger' => 'monolog.logger.red',
                        'steps' => [],
                    ]
                ]
            ],
            [
                'process' => [
                    'blue' => [
                        'logger' => 'monolog.logger.red',
                        'steps' => [],
                    ]
                ]
            ]
        ], $container);

        $this->assertEquals([
            'process' => [
                'red' => [
                    'logger' => 'monolog.logger.red',
                    'steps' => [],
                ],
                'blue' => [
                    'logger' => 'monolog.logger.red',
                    'steps' => [],
                ],
            ]
        ], $container->getParameter('darkilliant_process'));
    }
}