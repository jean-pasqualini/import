<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/8/18
 * Time: 8:10 AM.
 */

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
