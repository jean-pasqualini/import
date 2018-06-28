<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\StatDumper;


use Darkilliant\ProcessBundle\StatDumper\StatsCalculator;
use PHPUnit\Framework\TestCase;

class StatsCalculatorTest extends TestCase
{
    /** @var StatsCalculator */
    private $calculator;

    protected function setUp()
    {
        $this->calculator = new StatsCalculator();
    }

    public function testStat()
    {
        $this->assertEquals([
            'stats' => [
                'ClassName' => [
                    'position' => 1,
                    'global' => 3000.0,
                    'tendance' => '/\/',
                    'global_wait' => 0.0,
                    'best_times' => [1000.0, 2000.0],
                    'bad_times' => [1000.0, 2000.0],
                    'potential_rate' => '??',
                    'count_iteration' => 2,
                ]
            ],
            'total' => [
                'global' => 3000.0,
                'best_times' => [0, 0, 0],
                'bad_times' => [0, 0, 0],
                'global_wait' => 0,
                'position' => ' ',
                'potential_rate' => '??',
            ]
        ], $this->calculator->calcul([
            'ClassName' => [
                'last_start' => 11111112000.0,
                'time' => [1000.0, 2000.0],
                'real_time' => [],
                'position' => 1,
                'wait' => [0.0],
                'last_finish' => 11111114000.0,
            ]
        ]));
    }
}
