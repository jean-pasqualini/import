<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Transformer;

interface TransformerInterface
{
    public function transform($value, string $name = '', array $options = []);

    public function validate($value, string $name = '', array $options = []): bool;
}
