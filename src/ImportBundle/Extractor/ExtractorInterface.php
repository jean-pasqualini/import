<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Extractor;

interface ExtractorInterface
{
    public function extract(string $file): \Traversable;
}
