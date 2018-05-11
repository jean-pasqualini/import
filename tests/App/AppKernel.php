<?php

namespace App;

use Darkilliant\ImportBundle\DarkilliantImportBundle;
use Darkilliant\ProcessBundle\DarkilliantProcessBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new MonologBundle(),
            new DoctrineBundle(),
            new JMSSerializerBundle(),
            new DarkilliantImportBundle(),
            new DarkilliantProcessBundle(),
        ];
    }
}