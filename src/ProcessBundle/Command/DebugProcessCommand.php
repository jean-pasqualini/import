<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Command;

use Darkilliant\ProcessBundle\DependencyInjection\DarkilliantProcessExtension;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Darkilliant\ProcessBundle\Logger\InMemoryLogger;
use Darkilliant\ProcessBundle\Runner\StepDescripterRunner;

/**
 * @internal
 * Class DebugProcessCommand
 *
 * @codeCoverageIgnore
 */
class DebugProcessCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('debug:process')
            ->addArgument('process', InputArgument::OPTIONAL);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $outputHelper = new SymfonyStyle($input, $output);

        $config = $this->getContainer()->getParameter(DarkilliantProcessExtension::PARAMETER_NAME);

        if (!$input->getArgument('process')) {
            return $this->describeProcessList($config, $outputHelper);
        }

        return $this->describeProcess(
            $input->getArgument('process'),
            $outputHelper
        );
    }

    /**
     * @param array        $config
     * @param SymfonyStyle $outputHelper
     *
     * @throws \Exception
     */
    private function describeProcess(string $process, SymfonyStyle $outputHelper)
    {
        $outputHelper->section(sprintf('Describe %s', $process));

        /** @var StepDescripterRunner $stepDescripter */
        $stepDescripter = $this->getContainer()->get(StepDescripterRunner::class);
        /** @var InMemoryLogger $inMemoryLogger */
        $inMemoryLogger = $this->getContainer()->get(InMemoryLogger::class);

        $stepDescripter->run($stepDescripter->buildConfigurationProcess($process));

        $outputHelper->listing($inMemoryLogger->getMessages());
    }

    private function describeProcessList(array $config, SymfonyStyle $outputHelper)
    {
        $outputHelper->section('List of process');

        $outputHelper->listing(
            array_keys($config['process'])
        );
    }
}
