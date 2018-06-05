<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Debug\Debug;

require __DIR__.'/../../vendor/autoload.php';

Debug::enable();
$kernel = new AppKernel(getenv('SYMFONY_ENV') ?? 'dev', true);
$application = new Application($kernel);
$application->run(new ArgvInput(), new ConsoleOutput());