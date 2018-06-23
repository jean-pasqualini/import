<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 6/24/18
 * Time: 11:32 AM.
 */

namespace Darkilliant\ProcessBundle\Command;

use Darkilliant\ProcessBundle\StatDumper\StatCliDumper;
use Darkilliant\ProcessBundle\StatDumper\StatsCalculator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 * Class StatDumpCommand
 *
 * @codeCoverageIgnore
 */
class StatDumpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('process:stats');
        $this->addOption('zoom', null, InputOption::VALUE_NONE, 'zoom', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputHelper = new SymfonyStyle($input, $output);

        $data = json_decode(file_get_contents('stat.json'), true);

        $statCalculator = new StatsCalculator();

        $this->getContainer()->get(StatCliDumper::class)->dump(
            $statCalculator->calcul($data)['stats'] ?? [],
            $outputHelper
        );
    }
}
