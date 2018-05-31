<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;

class NotEmptyStringTransformer extends AbstractTransformer
{
    public function transform($value, string $name = '', array $options = [])
    {
        return $value;
    }

    public function validate($value, string $name = '', array $options = []): bool
    {
        if ('' == $value) {
            throw new TransformationException(
                sprintf(
                    ' %s is empty',
                    $name
                )
            );
        }

        return true;
    }
}
