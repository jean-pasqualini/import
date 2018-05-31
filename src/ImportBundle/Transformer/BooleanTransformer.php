<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;

class BooleanTransformer extends AbstractTransformer
{
    public function transform($value, string $name = '', array $options = [])
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function validate($value, string $name = '', array $options = []): bool
    {
        if (!in_array($value, ['0', '1'])) {
            throw new TransformationException(
                sprintf(
                    'Invalid %s not boolean (expected: 0, 1) (actual: %s)',
                    $name,
                    $value
                )
            );
        }

        return true;
    }
}
