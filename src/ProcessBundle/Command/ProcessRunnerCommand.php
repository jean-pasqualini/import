<?php

namespace Darkilliant\ProcessBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Darkilliant\ProcessBundle\Runner\StepRunner;

/**
 * @internal
 * Class ProcessRunnerCommand
 *
 * @codeCoverageIgnore
 */
class ProcessRunnerCommand extends ContainerAwareCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $stepRunner = $this->getContainer()->get(StepRunner::class);

        $outputHelper = new SymfonyStyle($input, $output);

        $processList = $input->getArgument('process');

        foreach ($processList as $processName) {
            $outputHelper->section(
                sprintf(
                    '<info>Launch process %s</info>',
                    $processName
                )
            );

            $stepRunner->run(
                $stepRunner->buildConfigurationProcess($processName),
                $this->resolveContext($input->getOption('context'))
            );

            $outputHelper->newLine();
        }
    }

    protected function configure()
    {
        $this->setName('process:run')
            ->addOption('context', 'c', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'context', [])
            ->addArgument('process', InputArgument::IS_ARRAY, 'process');
    }

    private function resolveContext(array $context)
    {
        $contextResolved = [];
        foreach ($context as $keyValue) {
            list($key, $value) = explode('=', $keyValue);

            $contextResolved[$key] = $value;
        }

        return $contextResolved;
    }
}
