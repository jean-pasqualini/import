<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;

class FloatTransformer extends AbstractTransformer
{
    public function transform($value, string $name = '', array $options = [])
    {
        return floatval($value);
    }

    public function validate($value, string $name = '', array $options = []): bool
    {
        if (false === filter_var($value, FILTER_VALIDATE_FLOAT)) {
            throw new TransformationException(
                sprintf(
                    'invalid %s is not a float (actual: %s)',
                    $name,
                    $value
                )
            );
        }

        return true;
    }
}
