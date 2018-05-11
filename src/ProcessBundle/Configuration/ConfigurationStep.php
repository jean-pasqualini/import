<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/8/18
 * Time: 11:04 AM.
 */

namespace Darkilliant\ProcessBundle\Configuration;

class ConfigurationStep
{
    private $next;

    private $options;

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
