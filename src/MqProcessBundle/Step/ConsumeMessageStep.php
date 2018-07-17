<?php

declare(strict_types=1);

namespace Darkilliant\MqProcessBundle\Step;

use Darkilliant\MqProcessBundle\Message\Message;
use Darkilliant\MqProcessBundle\Message\Provider\MessageProviderFactory;
use Darkilliant\MqProcessBundle\Message\Provider\MessageProviderInterface;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsumeMessageStep extends AbstractConfigurableStep implements IterableStepInterface
{
    /** @var Message */
    private $currentMessage;

    /** @var MessageProviderFactory */
    private $providerFactory;

    /**
     * @throws \AMQPConnectionException
     */
    public function __construct(MessageProviderFactory $providerFactory)
    {
        $this->providerFactory = $providerFactory;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['queue', 'exchange', 'client', 'ack_required', 'persistant', 'batch_count', 'requeue_on_fail']);
        $resolver->setDefault('client', 'amqp_lib');
        $resolver->setDefault('ack_required', true);
        $resolver->setDefault('persistant', true);
        $resolver->setDefault('batch_count', 1);
        $resolver->setDefault('requeue_on_fail', false);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $state->info('start consume');
        $state->setContext('queue', $state->getOptions()['queue']);
        $this->getProvider($state)->consume($state->getOptions()['ack_required']);
    }

    public function getProgress(ProcessState $state)
    {
        return 50;
    }

    public function count(ProcessState $state)
    {
        return 100;
    }

    public function next(ProcessState $state)
    {
        $this->currentMessage = null;

        $raw = $this->getCurrent($state)->getBody();

        $data = json_decode($raw, true);
        $state->info('consume message', ['data' => $data]);
        $state->setData($data);
    }

    public function valid(ProcessState $state)
    {
        return true;
    }

    public function onSuccessLoop(ProcessState $state)
    {
        if (!$state->getOptions()['ack_required']) {
            return;
        }

        $this->getProvider($state)->messageOk($this->getCurrent($state));
    }

    public function onFailedLoop(ProcessState $state)
    {
        if (!$state->getOptions()['ack_required']) {
            return;
        }

        $this->getProvider($state)->messageKo($this->getCurrent($state), $state->getOptions()['requeue_on_fail']);
    }

    private function getProviderName(ProcessState $state)
    {
        $options = $state->getOptions();

        return $options['queue'];
    }

    private function getProvider(ProcessState $state): MessageProviderInterface
    {
        return $this->providerFactory->factory($this->getProviderName($state), $state->getOptions());
    }

    private function getCurrent(ProcessState $state): Message
    {
        while (null === $this->currentMessage) {
            $message = $this->getProvider($state)->fetchMessage();
            if (null !== $message) {
                return $this->currentMessage = $message;
            }

            $this->getProvider($state)->waitChannel(false);
        }

        return $this->currentMessage;
    }
}
