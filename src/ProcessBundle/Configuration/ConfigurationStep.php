<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Configuration;

class ConfigurationStep
{
    private $next;

    private $options;

    private $service;

    /** @var bool */
    private $enabled;

    private function __construct(string $service, array $options, array $children, bool $enabled = true)
    {
        $this->service = $service;
        $this->options = $options;
        $this->children = array_map([ConfigurationStep::class, 'create'], $children);
        $this->enabled = $enabled;
    }

    public static function create(array $config)
    {
        return new self($config['service'], $config['options'] ?? [], $config['children'] ?? [], $config['enabled'] ?? true);
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

    public function isEnabled()
    {
        return $this->enabled;
    }
}
