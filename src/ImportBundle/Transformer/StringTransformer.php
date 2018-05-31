<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;

class StringTransformer extends AbstractTransformer
{
    public function transform($value, string $name = '', array $options = [])
    {
        return $value;
    }

    public function validate($value, string $name = '', array $options = []): bool
    {
        if (!is_string($value)) {
            throw new TransformationException(
                sprintf(
                    'invalid %s must be string type (actual: %s)',
                    $name,
                    $value
                )
            );
        }

        return true;
    }
}
