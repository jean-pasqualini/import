<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Configuration;

class ConfigurationStep
{
    /** @var array */
    private $children;

    /** @var array */
    private $options;

    /** @var string */
    private $service;

    private function __construct(string $service, array $options, array $children)
    {
        $this->service = $service;
        $this->options = $options;
        $this->children = array_map([ConfigurationStep::class, 'create'], $children);
    }

    public static function create(array $config)
    {
        return new self($config['service'], $config['options'] ?? [], $config['children'] ?? []);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getChildren(): array
    {
        return $this->children;
    }
}
