<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Registry\TransformerRegistry;
use Darkilliant\ImportBundle\Step\TransformStep;
use Darkilliant\ImportBundle\Transformer\StringTransformer;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class TransformStepTest extends TestCase
{
    /** @var TransformStep */
    private $step;

    /** @var TransformerRegistry|MockObject */
    private $registry;

    public function setUp()
    {
        $this->registry = $this->createMock(TransformerRegistry::class);
        $this->step = new TransformStep(PropertyAccess::createPropertyAccessor(), $this->registry);
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
        $state->setData([]);
        $state->setOptions([
           'transforms' => [
               ['type' => 'string', 'source' => 'red', 'target' => '[color]'],
           ],
        ]);

        $this->registry
            ->expects($this->once())
            ->method('get')
            ->with('string')
            ->willReturn(new StringTransformer());

        $this->step->execute($state);
        $this->assertEquals(['color' => 'red'], $state->getData());
    }
}
