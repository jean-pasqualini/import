<?php

namespace Darkilliant\ProcessBundle\Configuration;

class ConfigurationProcess
{
    /** @var ConfigurationStep[] */
    private $steps;

    private $logger;

    private function __construct(string $logger, array $steps)
    {
        $this->logger = $logger;
        $this->steps = $steps;
    }

    public static function create(array $config): ConfigurationProcess
    {
        return new self(
            $config['logger'] ?? 'process_logger_default',
            array_map([ConfigurationStep::class, 'create'], $config['steps'])
        );
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function getLogger(): string
    {
        return $this->logger;
    }
}
