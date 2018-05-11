<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Logger;

use Darkilliant\ProcessBundle\Logger\InMemoryLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class InMemoryLoggerTest extends TestCase
{
    /** @var InMemoryLogger */
    private $logger;

    public function setUp()
    {
        $this->logger = new InMemoryLogger();
    }

    public function testLog()
    {
        $this->logger->log(LogLevel::INFO, 'une maison {color}', ['color' => 'rouge']);

        $this->assertEquals(['une maison rouge'], $this->logger->getMessages());
    }
}