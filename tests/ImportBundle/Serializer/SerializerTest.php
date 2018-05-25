<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Serializer;

use App\Entity\Product;
use Darkilliant\ImportBundle\Serializer\Serializer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use JMS\Serializer\Serializer as JMSSerializer;

class SerializerTest extends TestCase
{
    /**
     * @var JMSSerializer|MockObject
     */
    private $jmsSerializer;

    /**
     * @var SymfonySerializer|MockObject
     */
    private $symfonySerializer;

    /**
     * @var Serializer
     */
    private $serializer;

    public function setUp()
    {
        $this->jmsSerializer = $this->createMock(JMSSerializer::class);
        $this->symfonySerializer = $this->createMock(SymfonySerializer::class);
        $this->serializer = new Serializer($this->symfonySerializer, $this->jmsSerializer);
    }

    public function testNotAvailableSerializerWhenNotInjected()
    {
        $this->expectException(\Exception::class);

        $serializer = new Serializer();
        $serializer->denormalize([], Product::class);
    }

    public function testNotAvailableSerializerWhenNotSelectorRecognizer()
    {
        $this->expectException(\Exception::class);

        $this->serializer->denormalize([], Product::class, 'unknow_strategy');
    }

    public function testUseJMSSerializerWhenAvailabeAndSelectorIsAuto()
    {
        $this->symfonySerializer
            ->expects($this->never())
            ->method('denormalize');

        $this->jmsSerializer
            ->expects($this->once())
            ->method('fromArray');

        $this->serializer->denormalize([], Product::class, 'auto');
    }

    public function testUseJMSSerializerWhenSelectorIsJms()
    {
        $this->symfonySerializer
            ->expects($this->never())
            ->method('denormalize');

        $this->jmsSerializer
            ->expects($this->once())
            ->method('fromArray');

        $this->serializer->denormalize([], Product::class, 'jms_serializer');
    }

    public function testUseSymfonySerializerWhenAvailabeAndSelectorIsSymfony()
    {
        $this->symfonySerializer
            ->expects($this->once())
            ->method('denormalize');

        $this->jmsSerializer
            ->expects($this->never())
            ->method('fromArray');

        $this->serializer->denormalize([], Product::class, 'symfony_serializer');
    }

}