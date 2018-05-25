<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/8/18
 * Time: 9:48 AM.
 */

namespace Darkilliant\ProcessBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\State\ProcessState;

abstract class AbstractConfigurableStep implements StepInterface
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired('progress_bar');
        $resolver->setDefault('progress_bar', false);

        return $resolver;
    }

    public function finalize(ProcessState $state)
    {
        return;
    }

    public function describe(ProcessState $state)
    {
        $state->info(sprintf('run step %s', get_class($this)));
    }

    public function count(ProcessState $state)
    {
        return;
    }

    public function getProgress(ProcessState $state)
    {
        return 0;
    }
}
