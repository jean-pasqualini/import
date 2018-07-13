<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Resolver;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @internal
 */
class OptionDynamicValueResolver
{
    /** @var PropertyAccessorInterface */
    private $accessor;

    public function __construct(PropertyAccessorInterface $accessor)
    {
        $this->accessor = $accessor;
    }

    public function resolve(array $options, array $context): array
    {
        foreach ($options as $optionName => $optionValue) {
            if (is_array($optionValue)) {
                $options[$optionName] = $this->resolve($optionValue, $context);
            } elseif (!is_string($optionValue)) {
                continue;
            } elseif (false !== strpos($optionValue, '@!')) {
                $key = substr($optionValue, 2);
                list($firstLevel, $secondLevel) = explode('->', $key);

                if (isset($context[$firstLevel][$secondLevel])) {
                    $options[$optionName] = $context[$firstLevel][$secondLevel];
                }
            } elseif (false !== strpos($optionValue, '@')) {
                $key = substr($optionValue, 1);

                if ($this->accessor->isReadable($context, $key)) {
                    $options[$optionName] = $this->accessor->getValue($context, $key);
                }
            }
        }

        return $options;
    }
}
