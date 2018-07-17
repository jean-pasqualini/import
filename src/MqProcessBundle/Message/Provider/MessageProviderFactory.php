<?php

namespace Darkilliant\MqProcessBundle\Message\Provider;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Container\ContainerInterface;

class MessageProviderFactory
{
    /** @var array */
    private $messageProvider = [];

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function factory(string $name, array $config)
    {
        if (isset($this->messageProvider[$name])) {
            return $this->messageProvider[$name];
        }

        if ('amqp_lib' === $config['client']) {
            /** @var $connexion AMQPStreamConnection */
            $connexion = $this->container->get($config['connexion'] ?? 'darkilliant_mqprocess_connection');

            return $this->messageProvider[$name] = new AmqpMessageProvider(
                $connexion->channel(),
                $config['queue'],
                $config['exchange'],
                $config['persistant'],
                $config['batch_count']
            );
        }

        return null;
    }
}
