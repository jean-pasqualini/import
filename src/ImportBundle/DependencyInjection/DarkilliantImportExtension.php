<?php

namespace Darkilliant\ImportBundle\DependencyInjection;

use Darkilliant\ImportBundle\Step\CsvExtractorStep;
use Darkilliant\ImportBundle\Step\DoctrinePersisterStep;
use Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep;
use Darkilliant\ImportBundle\Step\MappingTransformerStep;
use Darkilliant\ProcessBundle\Step\DebugStep;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @internal
 * Class DarkilliantImportExtension
 */
class DarkilliantImportExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $processCollection = [];
        foreach ($config['imports'] as $name => $importConfig) {
            $process = [
                'steps' => [
                    [
                        'service' => CsvExtractorStep::class,
                        'options' => [
                            'filepath' => $this->resolvePath($importConfig['source']),
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
                            'mapping' => $importConfig['mapping'],
                        ],
                    ],
                    [
                        'service' => LoadObjectNormalizedStep::class,
                        'options' => [
                            'entity_class' => $importConfig['entity_class'],
                        ],
                    ],
                    [
                        'service' => DoctrinePersisterStep::class,
                        'options' => [
                            'batch_count' => 20,
                        ],
                    ],
                ],
            ];

            $processCollection[$name] = $process;
        }

        $container->prependExtensionConfig('darkilliant_process', ['process' => $processCollection]);
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $yamlLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $container->setParameter('darkilliant_import_field_entity_resolver', $mergedConfig['fields_entity_resolver']);

        $yamlLoader->load('services.yml');
        $yamlLoader->load('transformers.yml');

        // Only when doctrine is in project
        if (in_array(DoctrineBundle::class, $container->getParameter('kernel.bundles'))) {
            $yamlLoader->load('symfony_serializer.yml');

            // Only when jms is in project
            if (in_array(JMSSerializerBundle::class, $container->getParameter('kernel.bundles'))) {
                $yamlLoader->load('jms_serializer.yml');
            }
        }
    }

    private function resolvePath(string $path): string
    {
        return str_replace('file://', '', $path);
    }
}
