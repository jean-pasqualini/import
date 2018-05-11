<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;

class DateTimeTransformer implements TransformerInterface
{
    public function transform($value, string $name = '', array $options = [])
    {
        return $value;
    }

    public function validate($value, string $name = '', array $options = []): bool
    {
        if (!$value instanceof \DateTime) {
            throw new TransformationException(
                sprintf(
                    'invalid %s is not an datetime (actual: %s)',
                    $name,
                    gettype($value)
                )
            );
        }

        return true;
    }
}
