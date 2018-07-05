<?php

namespace Darkilliant\ProcessBundle\LoopStateMarker;

use Darkilliant\ProcessBundle\State\ProcessState;
use Symfony\Component\Filesystem\Filesystem;

class FileLoopStateMarker
{
    /** @var Filesystem */
    private $fs;
    /** @var bool */
    private $removeOnSuccess = false;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function onStartLoop(ProcessState $state)
    {
        if (!$this->isEnabled($state)) {
            return;
        }

        $this->removeOnSuccess = $state->getOptions()['track_loop_state_remove_on_success'];

        $fileinfo = pathinfo($state->getContext('file_finder_current'));
        $this->createStructureProcessing($fileinfo['dirname']);

        $targetDirectory = $fileinfo['dirname'].'/_processing/wait';
        $targetPath = $targetDirectory.'/'.$fileinfo['basename'];

        $this->fs->rename(
            $state->getContext('file_finder_current'),
            $targetPath,
            true
        );

        $state->setData($targetPath);
    }

    public function onSuccessLoop(ProcessState $state)
    {
        if (!$this->isEnabled($state)) {
            return;
        }

        $fileinfo = pathinfo($state->getContext('file_finder_current'));
        $this->createStructureProcessing($fileinfo['dirname']);

        $sourceDirectory = $fileinfo['dirname'].'/_processing/wait';
        $targetDirectory = $fileinfo['dirname'].'/_processing/success';

        if ($this->removeOnSuccess) {
            $this->fs->remove($sourceDirectory.'/'.$fileinfo['basename']);

            return;
        }

        $this->fs->rename(
            $sourceDirectory.'/'.$fileinfo['basename'],
            $targetDirectory.'/'.$fileinfo['basename'],
            true
        );
    }

    public function onFailedLoop(ProcessState $state)
    {
        if (!$this->isEnabled($state)) {
            return;
        }

        $fileinfo = pathinfo($state->getContext('file_finder_current'));
        $this->createStructureProcessing($fileinfo['dirname']);

        $sourceDirectory = $fileinfo['dirname'].'/_processing/wait';
        $targetDirectory = $fileinfo['dirname'].'/_processing/failed';

        $this->fs->rename(
            $sourceDirectory.'/'.$fileinfo['basename'],
            $targetDirectory.'/'.$fileinfo['basename'],
            true
        );
    }

    private function isEnabled(ProcessState $state): bool
    {
        return $state->getOptions()['track_loop_state'] ?? false;
    }

    private function createStructureProcessing($directory)
    {
        // Create wait directory
        $targetDirectory = $directory.'/_processing/wait';
        if (!$this->fs->exists($targetDirectory)) {
            $this->fs->mkdir($targetDirectory);
        }
        // Create sucesss directory
        $targetDirectory = $directory.'/_processing/success';
        if (!$this->fs->exists($targetDirectory)) {
            $this->fs->mkdir($targetDirectory);
        }
        // Create fail directory
        $targetDirectory = $directory.'/_processing/failed';
        if (!$this->fs->exists($targetDirectory)) {
            $this->fs->mkdir($targetDirectory);
        }
    }
}
