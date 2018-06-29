<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidateObjectStep extends AbstractConfigurableStep
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['groups']);
        $resolver->setDefault('groups', []);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $violations = $this->validator->validate($state->getData(), null, $state->getOptions()['groups']);

        if ($violations->count() > 0) {
            $state->error('error with object', [
                'errors' => $this->violationToArray($violations),
            ]);
            $state->markFail();

            return;
        }
    }

    private function violationToArray(ConstraintViolationListInterface $violationList): array
    {
        $violations = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($violationList as $violation) {
            $violations[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $violations;
    }
}
