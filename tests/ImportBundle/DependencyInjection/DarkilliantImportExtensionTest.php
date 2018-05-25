<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\DependencyInjection;

use App\Entity\Product;
use Darkilliant\ImportBundle\DependencyInjection\DarkilliantImportExtension;
use Darkilliant\ImportBundle\Step\CsvExtractorStep;
use Darkilliant\ImportBundle\Step\CsvExtractorStepTest;
use Darkilliant\ImportBundle\Step\DoctrinePersisterStep;
use Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep;
use Darkilliant\ImportBundle\Step\MappingTransformerStep;
use Darkilliant\ProcessBundle\Step\DebugStep;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DarkilliantImportExtensionTest extends TestCase
{
    /** @var DarkilliantImportExtension */
    private $extension;

    public function setUp()
    {
        $this->extension = new DarkilliantImportExtension();
    }

    public function testLoadInternal()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.bundles', [
            DoctrineBundle::class,
            JMSSerializerBundle::class,
        ]);

        $this->extension->load([[
            'fields_entity_resolver' => [
                Product::class => ['ean'],
            ]
        ]], $container);

        $this->assertEquals(
            [
                Product::class => ['ean'],
            ],
            $container->getParameter('darkilliant_import_field_entity_resolver')
        );
    }

    public function testPrepend()
    {
        $container = new ContainerBuilder();

        $container->prependExtensionConfig('darkilliant_import', [
            'imports' => [
                'product' => [
                    'source' => 'file://fichier.csv',
                    'mapping' => [],
                    'entity_class' => Product::class,
                ]
            ]
        ]);

        $this->extension->prepend($container);

        $this->assertEquals(
            [[
                'process' => [
                    'product' => [
                        'steps' => [
                            [
                                'service' => CsvExtractorStep::class,
                                'options' => [
                                    'filepath' => 'fichier.csv',
                                    'colums_names' => null,
                                    'delimiter' => ';',
                                ],
                            ],
                            [
                                'service' => DebugStep::class,
                                'options' => [],
                            ],
                            [
                                'service' => MappingTransformerStep::class,
                                'options' => [
                                    'mapping' => [],
                                ],
                            ],
                            [
                                'service' => LoadObjectNormalizedStep::class,
                                'options' => [
                                    'entity_class' => Product::class,
                                ],
                            ],
                            [
                                'service' => DoctrinePersisterStep::class,
                                'options' => [
                                    'batch_count' => 20,
                                ],
                            ],
                        ]
                    ]
                ]
            ]],
            $container->getExtensionConfig('darkilliant_process')
        );
    }
}