<?php

declare(strict_types=1);

namespace Darkilliant\MqProcessBundle\Message\Publisher;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Container\ContainerInterface;

class MessagePublisherFactory
{
    /** @var array */
    private $messagePublisher = [];

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function factory(string $name, array $config)
    {
        if (isset($this->messagePublisher[$name])) {
            return $this->messagePublisher[$name];
        }

        if ('amqp_lib' === $config['client']) {
            /** @var $connexion AMQPStreamConnection */
            $connexion = $this->container->get($config['connexion'] ?? 'darkilliant_mqprocess_connection');

            return $this->messagePublisher[$name] = new AmqpMessagePublisher(
                $connexion->channel(),
                $config['queue'],
                $config['exchange'],
                $config['persistant']
            );
        }

        return null;
    }
}
