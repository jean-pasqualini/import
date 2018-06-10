<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorFilter extends AbstractFilter
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['validator', 'groups', 'options']);
        $resolver->setDefault('groups', []);
        $resolver->setDefault('options', []);

        return parent::configureOptionResolver($resolver);
    }

    /**
     * @throws \Exception
     */
    public function isAccept($value, array $options): bool
    {
        $constraintViolationList = $this->validator->validate($value, $this->factoryConstraint($options['validator'], $options['options']), $options['groups']);

        return $constraintViolationList->count() < 1;
    }

    /**
     * @throws \Exception
     */
    private function factoryConstraint($className, $options): Constraint
    {
        $instance = new $className($options);
        if (!$instance instanceof Constraint) {
            throw new \Exception(sprintf('class %s is not constraint', $className));
        }

        return $instance;
    }
}
