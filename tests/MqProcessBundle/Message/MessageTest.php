<?php

declare(strict_types=1);

namespace Tests\Darkilliant\MqProcessBundle\Message;

use Darkilliant\MqProcessBundle\Message\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    /** @var Message */
    private $message;

    protected function setUp()
    {
        $this->message = new Message('{}', ['c' => 1], 5);
    }

    public function testGetBody()
    {
        $this->assertEquals('{}', $this->message->getBody());
    }

    public function testGetProperties()
    {
        $this->assertEquals(['c' => 1], $this->message->getProperties());
    }

    public function testGetId()
    {
        $this->assertEquals(5, $this->message->getId());
    }
}
