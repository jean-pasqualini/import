<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/8/18
 * Time: 9:46 AM.
 */

namespace Darkilliant\ProcessBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\State\ProcessState;

interface StepInterface
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver;

    public function execute(ProcessState $state);

    public function finalize();

    public function describe(ProcessState $state);
}
