<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Step;

use App\Entity\Product;
use Darkilliant\ImportBundle\Loader\ObjectLoader;
use Darkilliant\ImportBundle\Step\LoadObjectStep;
use Darkilliant\ImportBundle\TargetResolver\DoctrineTargetResolver;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoadObjectStepTest extends TestCase
{
    /** @var LoadObjectStep */
    private $step;

    /** @var ObjectLoader|MockObject */
    private $objectLoader;
    /** @var DoctrineTargetResolver|MockObject */
    private $targetResolver;

    public function setUp()
    {
        $this->objectLoader = $this->createMock(ObjectLoader::class);
        $this->targetResolver = $this->createMock(DoctrineTargetResolver::class);
        $this->step = new LoadObjectStep($this->objectLoader, $this->targetResolver);
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
        $state->setOptions([
            'target_resolver' => [],
            'target_mapping' => [],
        ]);
        $state->setData([
            'title' => 'un',
        ]);

        $product = new Product();
        $product->setId(5);

        $this->objectLoader
            ->expects($this->once())
            ->method('load')
            ->with($product, ['title' => 'un'], [])
            ->willReturn($product);
        $this->targetResolver
            ->expects($this->once())
            ->method('resolve')
            ->with([])
            ->willReturn($product);

        $this->step->execute($state);

        $this->assertEquals($product, $state->getData());
    }

    public function testDescribe()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'target_resolver' => [
                'entityClass' => Product::class
            ]
        ]);

        $logger
            ->expects($this->once())
            ->method('log')
            ->with(
                LogLevel::INFO,
                'create object {class} with array data',
                [
                    'class' => Product::class,
                ]
            );

        $this->step->describe($state);
    }
}