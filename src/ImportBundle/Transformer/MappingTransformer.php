<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Registry\TransformerRegistry;

class MappingTransformer
{
    /** @var TransformerRegistry */
    private $registry;

    public function __construct(TransformerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function transform(array $data, array $mapping)
    {
        foreach ($mapping as $key => $config) {
            $data[$key] = (is_array($config))
                ? $this->applyTransformer((string) $key, $config['value'], $config['transformers'])
                : $config;
        }

        return $data;
    }

    private function applyTransformer(string $name, $value, $transformers)
    {
        foreach ($transformers as $transformerKey => $transformerConfig) {
            $transformerName = (is_array($transformerConfig)) ? $transformerKey : $transformerConfig;
            $transformerOptions = (is_array($transformerConfig)) ? $transformerConfig : [];

            $transformer = $this->registry->get($transformerName);
            $transformer->validate($value, $name, $transformerOptions);

            $value = $transformer->transform($value, $name, $transformerOptions);
        }

        return $value;
    }
}
