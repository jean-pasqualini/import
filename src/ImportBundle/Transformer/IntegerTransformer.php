<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;

class IntegerTransformer extends AbstractTransformer
{
    public function transform($value, string $name = '', array $options = [])
    {
        return intval($value);
    }

    public function validate($value, string $name = '', array $options = []): bool
    {
        if (is_int($value)) {
            return true;
        }

        if (false === ctype_digit($value)) {
            throw new TransformationException(
                sprintf(
                    'invalid %s is not an integer (actual: %s)',
                    $name,
                    $value
                )
            );
        }

        return true;
    }
}
