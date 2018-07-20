<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\WhereBuilder;

interface WhereBuilderInterface
{
    public function buildWhere(string $class, array $config, array $data): array;
}
