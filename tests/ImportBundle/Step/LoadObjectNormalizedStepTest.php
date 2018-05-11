<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Step;

use App\Entity\Product;
use Darkilliant\ImportBundle\Serializer\Serializer;
use Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoadObjectNormalizedStepTest extends TestCase
{
    /** @var LoadObjectNormalizedStep */
    private $step;

    /** @var Serializer|MockObject */
    private $denormalizer;

    public function setUp()
    {
        $this->denormalizer = $this->createMock(Serializer::class);

        $this->step = new LoadObjectNormalizedStep($this->denormalizer);
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    public function testExecute()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['entity_class' => Product::class, 'serializer' => 'auto']);

        $product = new Product();
        $product->setId(5);

        $this->denormalizer
            ->expects($this->once())
            ->method('denormalize')
            ->willReturn($product);

        $this->step->execute($state);

        $this->assertInstanceOf(Product::class, $state->getData());
        $this->assertEquals($product, $state->getData());
        $this->assertEquals(Product::class, $state->getContext('class'));
        $this->assertEquals(5, $state->getContext('id'));
    }

    public function testDescribe()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['entity_class' => Product::class, 'serializer' => 'auto']);

        $logger
            ->expects($this->once())
            ->method('log')
            ->with(
                LogLevel::INFO,
                'create object {class} with array data',
                ['class' => Product::class]
            );

        $this->step->describe($state);
    }
}