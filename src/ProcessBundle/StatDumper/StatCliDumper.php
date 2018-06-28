<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\StatDumper;

use Symfony\Component\Console\Style\SymfonyStyle;

class StatCliDumper
{
    const TABLE_HEADERS = ['class', '01 BEST', '02 BEST', '03 BEST', '01 BAD', '02 BAD', '03 BAD', 'TOTAL RUN', 'TOTAL WAIT'];

    private $max = null;

    public function dump(array $stats, SymfonyStyle $outputHelper)
    {
        $this->dumpTable($outputHelper, $stats);
        $this->dumpPipe($outputHelper, $stats);

        $outputHelper->writeln('');
    }

    private function dumpTable(SymfonyStyle $outputHelper, array $stats)
    {
        $lines = [];
        $this->max = max(array_column($stats, 'global'));

        foreach ($stats as $class => $values) {
            $class = $this->getShortClassName($class);

            // Fetch times and format for display
            $times = array_merge(
                array_pad($values['best_times'], 3, -1),
                array_pad($values['bad_times'], 3, -1),
                [$values['global'], $values['global_wait']]
            );
            $times = $this->formatTimes($times);

            $title = sprintf(
                '%s. (%s) %s (%sx)',
                $values['position'],
                $values['tendance'],
                $class,
                number_format($values['count_iteration'])
            );

            $lines[] = array_merge([$title], $times);
        }

        $outputHelper->table(self::TABLE_HEADERS, $lines);
    }

    private function dumpPipe(SymfonyStyle $outputHelper, array $stats)
    {
        $outputHelper->write($this->generatePipe($stats));
    }

    private function formatTimes(array $times): array
    {
        // Best times
        $times[0] = $this->formatTime($times[0], 100);
        $times[1] = $this->formatTime($times[1], 100);
        $times[2] = $this->formatTime($times[2], 100);

        // Bad times
        $times[3] = $this->formatTime($times[3], 100);
        $times[4] = $this->formatTime($times[4], 100);
        $times[5] = $this->formatTime($times[5], 100);

        // Total run
        $times[6] = $this->formatTime($times[6]);
        // Total wait
        $times[7] = $this->formatTime($times[7]);

        return $times;
    }

    private function formatTime($time, $seuil = null): string
    {
        $time = (int) $time;
        if (-1 === $time) {
            return '~ ms';
        }

        return (($seuil && $time > $seuil) || $time == $this->max) ? '<error>'.$time.' ms</error>' : $time.' ms';
    }

    private function getShortClassName(string $fqcn): string
    {
        $path = explode('\\', $fqcn);

        return array_pop($path);
    }

    private function generatePipe($stats)
    {
        $waits = array_combine(array_keys($stats), array_column($stats, 'global_wait'));
        $maxWait = max($waits);

        // Convert all wait times to float
        $waits = array_map(function ($item) { return (int) $item; }, $waits);
        // Skip tasks not wait
        $waits = array_filter($waits, function ($item) { return 0 != $item; });

        $reference = $maxWait / 100;

        $pipe = [];

        foreach ($waits as $class => $ms) {
            $countPercents = (int) ($ms / $reference);

            if ($countPercents < 1) {
                $countPercents = 1;
            }

            $pipe = array_merge($pipe, $this->generatePartPipe($class, $countPercents));
        }

        return implode(PHP_EOL, $pipe);
    }

    private function generatePartPipe($class, $countPercents)
    {
        $pipe = [];

        // Create one part of pipe for represente speed of task
        $pipe = array_merge($pipe, array_pad([], 9, $this->generateBar($countPercents)));
        $pipe = array_merge($pipe, [$this->generateBarClass($class, $countPercents)]);
        $pipe = array_merge($pipe, array_pad([], 9, $this->generateBar($countPercents)));

        return $pipe;
    }

    private function generateBar(int $countPercents): string
    {
        $bar = str_repeat(' ', 10);
        $bar .= str_repeat(' ', (int) ((100 - $countPercents) / 2));
        $bar .= "\e[32m\e[42m".str_repeat('#', $countPercents)."\e[0m";

        return $bar;
    }

    private function generateBarClass(string $class, int $countPercents): string
    {
        $size = strlen($class);
        $bar = str_repeat(' ', 10);
        $bar .= str_repeat(' ', (int) ((100 - $countPercents) / 2));
        $bar .= "\e[42m".$class."\e[0m";
        if ($countPercents > $size) {
            $bar .= "\e[32m\e[42m".str_repeat('#', $countPercents - $size)."\e[0m";
        }

        return $bar;
    }
}
