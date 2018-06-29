<?php

namespace Darkilliant\ProcessBundle\StatDumper;

class StatsCalculator
{
    public function calcul(array $dataCollected): array
    {
        $calculatedStats = [];

        foreach ($dataCollected as $class => $stat) {
            $calculatedStats[$class] = $this->calculForOneStep($stat);
        }

        return [
            'stats' => $calculatedStats,
            'total' => $this->calculTotals($calculatedStats),
        ];
    }

    private function isEventfull($times)
    {
        $moved = false;
        $last = null;
        foreach ($times as $time) {
            $time = (int) $time;
            if (null !== $last && $last != $time) {
                $moved = true;
                break;
            }
            $last = $time;
        }

        return $moved;
    }

    private function calculForOneStep($stat)
    {
        $calculatedStat = [];
        $calculatedStat['global'] = array_sum($stat['time']);

        $calculatedStat['tendance'] = (!$this->isEventfull($stat['time'])) ? '~~~' : '/\/';
        $calculatedStat['global_wait'] = array_sum($stat['wait']);
        asort($stat['time'], SORT_NUMERIC);
        $calculatedStat['best_times'] = array_slice($stat['time'], 0, 3, true);
        arsort($stat['time'], SORT_NUMERIC);
        $calculatedStat['bad_times'] = array_slice($stat['time'], 0, 3, true);
        $calculatedStat['potential_rate'] = '??';
        $calculatedStat['position'] = $stat['position'];
        $calculatedStat['count_iteration'] = count($stat['time']);

        return $calculatedStat;
    }

    private function calculTotals($calculatedStats)
    {
        return [
            'global' => array_sum(array_column($calculatedStats, 'global')),
            'best_times' => [0, 0, 0],
            'bad_times' => [0, 0, 0],
            'global_wait' => array_sum(array_column($calculatedStats, 'global_wait')),
            'position' => ' ',
            'potential_rate' => '??',
        ];
    }
}
