<?php

namespace Darkilliant\ImportBundle\Serializer;

use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use JMS\Serializer\Serializer as JMSSerializer;

/**
 * @internal
 */
class Serializer
{
    /** @var SymfonySerializer */
    private $symfonySerializer;

    /** @var JMSSerializer */
    private $jmsSerializer;

    public function __construct(SymfonySerializer $symfonySerializer = null, JMSSerializer $jmsSerializer)
    {
        $this->symfonySerializer = $symfonySerializer;
        $this->jmsSerializer = $jmsSerializer;
    }

    /**
     * @throws \Exception
     */
    public function denormalize($data, $class, string $selector = 'auto')
    {
        $serializer = $this->getSerializer($selector);

        if (null === $serializer) {
            throw new \Exception('unknow serializer');
        }

        if ($serializer instanceof JMSSerializer) {
            return $serializer->fromArray($data, $class);
        }

        return $serializer->denormalize($data, $class);
    }

    private function getSerializer(string $selector)
    {
        if (null === $this->symfonySerializer && null === $this->jmsSerializer) {
            return null;
        }

        if ('auto' === $selector) {
            return $this->jmsSerializer ?? $this->symfonySerializer;
        }

        if ('jms_serializer' === $selector) {
            return $this->jmsSerializer;
        }

        if ('symfony_serializer' === $selector) {
            return $this->symfonySerializer;
        }

        return null;
    }
}
