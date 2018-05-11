<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Exception;

use Darkilliant\ImportBundle\Exception\TransformationException;
use PHPUnit\Framework\TestCase;

class TranformationExceptionTest extends TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf(
            TransformationException::class,
            new TransformationException()
        );
    }
}