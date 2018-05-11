<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;

class ContainKeysTransformer implements TransformerInterface
{
    public function transform($value, string $name = '', array $options = [])
    {
        return $value;
    }

    public function validate($value, string $name = '', array $options = []): bool
    {
        if (count(array_diff($options['keys'], array_keys($value))) > 0) {
            throw new TransformationException(
                sprintf(
                    'invalid keys (expected: %s) (actual: %s)',
                    implode(',', $options['keys']),
                    implode(',', array_keys($value))
                )
            );
        }

        return true;
    }
}
