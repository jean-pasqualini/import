<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Console;

use Darkilliant\ProcessBundle\Console\ProgressBar;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\Darkilliant\ProcessBundle\FakeOutput;

class ProgressBarTest extends TestCase
{
    /** @var ProgressBar */
    private $progressBar;

    /** @var FakeOutput */
    private $output;

    const INIT_TIME = 1526811735;

    public function setUp()
    {
        $this->output = new FakeOutput();
        $this->progressBar = new ProgressBar();
        $this->progressBar->setOutput($this->output);

        ClockMock::withClockMock(self::INIT_TIME);
    }

    public static function setUpBeforeClass()
    {
        ClockMock::register(ProgressBar::class);
    }

    public static function tearDownAfterClass()
    {
        ClockMock::withClockMock(false);
    }

    public function testRun()
    {
        $this->progressBar->create(10, 'home');
        $this->assertOutputContains('home');
        $this->assertOutputContains('0/10');
        $this->assertOutputContains('MEMORY -1 -1 -1');
        $this->assertOutputContains('ITEMS -1 -1 -1');

        ClockMock::sleep(1);
        $this->progressBar->setProgress(2);
        $this->assertOutputContains('2/10');

        ClockMock::sleep(1);
        $this->progressBar->setProgress(4);
        $this->assertOutputContains('4/10');

        ClockMock::sleep(1);
        $this->progressBar->advance();
        $this->assertOutputContains('5/10');

        $this->progressBar->finish();
        $this->assertOutputContains('10/10');
    }

    public function testRunWithNoUpdate()
    {
        $this->progressBar->create(10, 'home');
        $this->assertOutputContains('home');
        $this->assertOutputContains('0/10');
        $this->assertOutputContains('MEMORY -1 -1 -1');
        $this->assertOutputContains('ITEMS -1 -1 -1');

        $this->progressBar->setProgress(2);
        $this->assertOutputContains('0/10');
    }

    public function testUpdateWithoutCreate()
    {
        $this->progressBar->setProgress(2);
        $this->assertEquals('', $this->output->fetch());
    }

    public function testKeepTimelimitLimit()
    {
        $this->progressBar->create(10, 'home');

        for ($i = 1; $i <= 30; $i++) {
            ClockMock::sleep(1);
            $this->progressBar->setProgress(2);
        }

        $this->assertOutputContains('2/10');
    }

    public function testNotShowBarWhenVerbose()
    {
        $this->output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        $this->progressBar->create(10, 'home');

        $this->assertEquals('', $this->output->fetch());
    }

    public function testShowBarWhenNotVerbose()
    {
        $this->output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
        $this->progressBar->create(10, 'home');

        $this->assertNotEquals('', $this->output->fetch());
    }

    private function assertOutputContains($expected)
    {
        $actual = preg_replace(
            '#\\x1b[[][^A-Za-z]*[A-Za-z]#',
            '',
            $this->output->fetch()
        );
        $this->assertContains(str_replace('#', ' ', $expected), $actual);
    }
}