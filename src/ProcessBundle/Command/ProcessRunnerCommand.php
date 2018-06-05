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
        if ($input->getOption('force-color')) {
            $output->getFormatter()->setDecorated(true);
        }

        $stepRunner = $this->getContainer()->get(StepRunner::class);

        $outputHelper = new SymfonyStyle($input, $output);

        $processList = $input->getArgument('process');

        foreach ($processList as $processName) {
            $data = [];
            if ($input->getOption('input-from-stdin')) {
                $body = stream_get_contents(STDIN);
                $data = json_decode($body, true);
            }

            $outputHelper->section(
                sprintf(
                    '<info>Launch process %s</info>',
                    $processName
                )
            );

            $stepRunner->run(
                $stepRunner->buildConfigurationProcess($processName),
                $this->resolveContext($input->getOption('context')),
                $data,
                $input->getOption('dry-run')
            );

            $outputHelper->newLine();
        }
    }

    protected function configure()
    {
        $this->setName('process:run')
            ->addOption('context', 'c', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'context', [])
            ->addOption('input-from-stdin', null, InputOption::VALUE_NONE, 'enable data pass in stdin with json body')
            ->addOption('force-color', null, InputOption::VALUE_NONE, 'force use color when not autodetect support')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'dry run')
            ->addArgument('process', InputArgument::IS_ARRAY, 'process');
    }

    private function resolveContext(array $context)
    {
        $contextResolved = [];

        // Use context option
        foreach ($context as $keyValue) {
            list($key, $value) = explode('=', $keyValue);

            $contextResolved[$key] = $value;
        }

        return $contextResolved;
    }
}
