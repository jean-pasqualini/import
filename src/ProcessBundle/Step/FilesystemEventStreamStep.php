<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Process;

class FilesystemEventStreamStep extends AbstractConfigurableStep implements IterableStepInterface
{
    /** @var Process */
    private $process;
    private $events = [];

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['folder', 'event_name', 'recursive']);
        $resolver->setDefault('recursive', false);

        $resolver->setAllowedValues('event_name', [
            'access', 'modify', 'attrib', 'close_write',
            'close_nowrite', 'close', 'open', 'moved_to',
            'moved_from', 'move', 'create', 'delete',
            'delete_self', 'unmount',
        ]);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $command = sprintf(
            'inotifywait %s --csv -q --monitor -e %s %s',
            $state->getOptions()['recursive'] ? '-r' : '',
            $state->getOptions()['event_name'],
            $state->getOptions()['folder']
        );

        $this->process = $this->getProcess($command);
        $this->process->setTimeout(null);
    }

    public function next(ProcessState $state)
    {
        $current = null;

        while (!$current) {
            $currentOutput = $this->process->getIncrementalOutput();

            $lines = explode(PHP_EOL, $currentOutput);
            $lines = array_filter($lines, function ($item) { return !empty($item); });

            $state->debug('{count} lines', ['count' => count($lines)]);

            foreach ($lines as $line) {
                list($folder, $events, $file) = str_getcsv($line);
                $absoluteFile = $folder.$file;
                $events = explode(',', $events);

                $this->events[] = $data = [
                    'events' => $events,
                    'absolute_file' => $absoluteFile,
                    'folder' => $folder,
                    'file' => $file,
                ];

                $state->debug('event {event} on {file}', [
                    'event' => $data['events'][0],
                    'file' => $data['absolute_file'],
                ]);
            }

            $current = array_shift($this->events);

            if (!$current) {
                $state->info('Waiting FileSystem Event...');
                sleep(1);
            }
        }

        $state->setData($current);
    }

    public function valid(ProcessState $state)
    {
        if (!$this->process->isStarted()) {
            $this->process->start();
        }

        return $this->process->isRunning();
    }

    public function getProgress(ProcessState $state)
    {
        return 1;
    }

    public function count(ProcessState $state)
    {
        return 1;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getProcess(string $commandLine): Process
    {
        return new Process($commandLine);
    }
}
