<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\FilesystemEventWaitingStep;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilesystemEventWaitingStepTest extends TestCase
{
    /** @var FilesystemEventWaitingStep */
    private $step;

    protected function setUp()
    {
        $this->step = new FilesystemEventWaitingStep();
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
        $folder = '/tmp/'.uniqid().'/';
        mkdir($folder, 0777, true);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'recursive' => false,
            'timeout' => 1,
            'event_name' => 'close_write',
            'folder' => $folder,
        ]);

        $this->assertNull($this->step->execute($state));
    }
}
