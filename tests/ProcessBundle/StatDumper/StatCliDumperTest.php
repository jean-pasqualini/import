<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\StatDumper;

use Darkilliant\ProcessBundle\StatDumper\StatCliDumper;
use Darkilliant\ProcessBundle\Step\DebugStep;
use Darkilliant\ProcessBundle\Step\IterateArrayStep;
use Darkilliant\ProcessBundle\Step\PredefinedDataStep;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;

class StatCliDumperTest extends TestCase
{
    /** @var StatCliDumper */
    private $dumper;

    protected function setUp()
    {
        $this->dumper = new StatCliDumper();
    }

    public function testDump()
    {
        $outputHelper = $this->createMock(SymfonyStyle::class);

        $outputHelper
            ->expects($this->once())
            ->method('table')
            ->with(
                ['class', '01 BEST', '02 BEST', '03 BEST', '01 BAD', '02 BAD', '03 BAD', 'TOTAL RUN', 'TOTAL WAIT'],
                [
                    ['1. (~~~) DebugStep (1x)', '<error>1000 ms</error>', '~ ms', '~ ms', '<error>1000 ms</error>', '~ ms', '~ ms', '<error>1000 ms</error>', '5000 ms'],
                    ['1. (~~~) PredefinedDataStep (1x)', '<error>1000 ms</error>', '~ ms', '~ ms', '<error>1000 ms</error>', '~ ms', '~ ms', '500 ms', '5000 ms'],
                    ['1. (~~~) IterateArrayStep (1x)', '<error>1000 ms</error>', '~ ms', '~ ms', '<error>1000 ms</error>', '~ ms', '~ ms', '500 ms', '1 ms'],
                ]
            );

        $this->dumper->dump([
            DebugStep::class => [
                'last_start' => 11111111000.0,
                'real_time' => [],
                'position' => 1,
                'last_finish' => 11111112000.0,
                'global' => 1000.0,
                'tendance' => '~~~',
                'global_wait' => 5000,
                'best_times' => [1000.0],
                'bad_times' => [1000.0],
                'potential_rate' => '??',
                'count_iteration' => 1,
            ],
            PredefinedDataStep::class => [
                'last_start' => 11111111000.0,
                'real_time' => [],
                'position' => 1,
                'last_finish' => 11111112000.0,
                'global' => 500.0,
                'tendance' => '~~~',
                'global_wait' => 5000,
                'best_times' => [1000.0],
                'bad_times' => [1000.0],
                'potential_rate' => '??',
                'count_iteration' => 1,
            ],
            IterateArrayStep::class => [
                'last_start' => 11111111000.0,
                'real_time' => [],
                'position' => 1,
                'last_finish' => 11111112000.0,
                'global' => 500.0,
                'tendance' => '~~~',
                'global_wait' => 1,
                'best_times' => [1000.0],
                'bad_times' => [1000.0],
                'potential_rate' => '??',
                'count_iteration' => 1,
            ]
        ], $outputHelper);
    }
}
