<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilesystemEventWaitingStep extends AbstractConfigurableStep
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['folder', 'event_name', 'timeout', 'recursive']);
        $resolver->setDefault('timeout', 0);
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
        $state->info('filesystem -> wait event {event_name} on folder {folder}', $state->getOptions());

        $command = sprintf(
            'inotifywait %s -q --timeout=%s -e %s %s',
            $state->getOptions()['recursive'] ? '-r' : '',
            $state->getOptions()['timeout'],
            $state->getOptions()['event_name'],
            $state->getOptions()['folder']
        );

        exec($command);
    }
}
