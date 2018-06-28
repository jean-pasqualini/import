<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Persister;

interface PersisterInterface
{
    public function persist($entity, $batchSize = 20);

    public function finalize();
}
