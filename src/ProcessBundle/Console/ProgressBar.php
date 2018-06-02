<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Console;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar as SymfonyProgressBar;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 * Class ProgressBar
 */
class ProgressBar
{
    const INTERVAL_MONITORING = [1, 10, 20];
    /** @var OutputInterface */
    private $output;
    /** @var SymfonyProgressBar */
    private $progressBar;

    private $timelineMemory = [];
    private $intervalMemory = [-1, -1, -1];
    private $maxMemory;

    private $timelineItemPerSecond = [];
    private $intervalItemPerSecond = [-1, -1, -1];
    private $minItemPerSecond;

    private $lastTimeUpdated;
    private $timeElapsed = 0;
    private $previousItemCount = 0;

    public function setOutput($output)
    {
        $this->output = $output;
    }

    public function create($size, $title = 'Progression', $maxMemory = 30, $minItemPerSecond = 50)
    {
        $this->init();
        $this->maxMemory = $maxMemory;
        $this->minItemPerSecond = $minItemPerSecond;

        $this->progressBar =
            ($this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL)
            ? new SymfonyProgressBar(new NullOutput(), $size)
            : new SymfonyProgressBar($this->output, $size);
        SymfonyProgressBar::setPlaceholderFormatterDefinition('memory', [$this, 'renderRightbar']);
        $this->progressBar->setFormat(" \033[44;37m %title:-37s% \033[0m\n %current%/%max% %bar% %percent:3s%%\n 🏁  %remaining:-10s% %memory:37s%");
        $this->progressBar->setBarCharacter($done = "\033[32m=\033[0m");
        $this->progressBar->setEmptyBarCharacter($empty = "\033[31m \033[0m");
        $this->progressBar->setProgressCharacter($progress = "\033[32m>\033[0m");
        $this->progressBar->setBarWidth(400);
        $this->progressBar->setMessage($title, 'title');
        $this->progressBar->start();
    }

    public function renderRightbar()
    {
        return sprintf(
            'MEMORY %s / ITEMS %s',
            implode(' ', $this->intervalMemory),
            implode(' ', $this->intervalItemPerSecond)
        );
    }

    public function setProgress($progress)
    {
        if ($this->isUpdate()) {
            $this->updateMemory();
            $this->updateItemPerSecond();
            $this->progressBar->setProgress($progress);
        }
    }

    public function advance()
    {
        if ($this->isUpdate()) {
            $this->updateMemory();
            $this->updateItemPerSecond();
            $this->progressBar->advance();
        }
    }

    public function finish()
    {
        $this->progressBar->finish();
    }

    private function init()
    {
        $this->timelineMemory = [];
        $this->intervalMemory = [-1, -1, -1];

        $this->timelineItemPerSecond = [];
        $this->intervalItemPerSecond = [-1, -1, -1];

        $this->lastTimeUpdated = time();
        $this->previousItemCount = 0;
    }

    private function isUpdate()
    {
        if (null === $this->progressBar) {
            return;
        }

        $time = time();

        if ($time > $this->lastTimeUpdated) {
            $this->timeElapsed = $time - $this->lastTimeUpdated;
            $this->lastTimeUpdated = $time;

            return true;
        }

        return false;
    }

    private function updateMemory()
    {
        $time = time();

        if (count($this->timelineMemory) > self::INTERVAL_MONITORING[2]) {
            $this->timelineMemory = array_slice($this->timelineMemory, 1, null, true);
        }

        $this->timelineMemory[$time] = $now = memory_get_usage(true);

        $this->intervalMemory[0] = $this->formatMemory(
            $this->timelineMemory[$time - self::INTERVAL_MONITORING[0]] ?? $now,
            self::INTERVAL_MONITORING[0]
        );
        $this->intervalMemory[1] = $this->formatMemory(
            $this->timelineMemory[$time - self::INTERVAL_MONITORING[1]] ?? -1,
            self::INTERVAL_MONITORING[1]
        );
        $this->intervalMemory[2] = $this->formatMemory(
            $this->timelineMemory[$time - self::INTERVAL_MONITORING[2]] ?? -1,
            self::INTERVAL_MONITORING[2]
        );
    }

    private function updateItemPerSecond()
    {
        $time = time();

        if (count($this->timelineItemPerSecond) > self::INTERVAL_MONITORING[2]) {
            $this->timelineItemPerSecond = array_slice($this->timelineItemPerSecond, 1, null, true);
        }

        $this->timelineItemPerSecond[$time] = $now = ($this->progressBar->getProgress() - $this->previousItemCount) / $this->timeElapsed;
        $this->previousItemCount = $this->progressBar->getProgress();

        $this->intervalItemPerSecond[0] = $this->formatItemsCount(
            $this->timelineItemPerSecond[$time - self::INTERVAL_MONITORING[0]] ?? $now,
            self::INTERVAL_MONITORING[0]
        );
        $this->intervalItemPerSecond[1] = $this->formatItemsCount(
            $this->timelineItemPerSecond[$time - self::INTERVAL_MONITORING[1]] ?? -1,
            self::INTERVAL_MONITORING[1]
        );
        $this->intervalItemPerSecond[2] = $this->formatItemsCount(
            $this->timelineItemPerSecond[$time - self::INTERVAL_MONITORING[2]] ?? -1,
            self::INTERVAL_MONITORING[2]
        );
    }

    private function formatMemory($memory, $time)
    {
        $colors = (($memory / 1000) > $this->maxMemory * 1000) ? '41;37' : '44;37';
        $message = $time.'s = '.Helper::formatMemory($memory);

        return "\033[".$colors.'m '.$message." \033[0m";
    }

    private function formatItemsCount($itemsCount, $time)
    {
        $colors = ($itemsCount < $this->minItemPerSecond) ? '41;37' : '44;37';
        $message = $time.'s = '.$itemsCount;

        return "\033[".$colors.'m '.$message."/s \033[0m";
    }
}
