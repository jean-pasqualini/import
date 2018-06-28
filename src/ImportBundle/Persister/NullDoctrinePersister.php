<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Persister;

/**
 * @internal
 */
class NullDoctrinePersister implements PersisterInterface
{
    public function persist($entity, $batchSize = 20)
    {
        return;
    }

    public function finalize()
    {
        return;
    }
}
