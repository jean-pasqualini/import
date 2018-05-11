<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/8/18
 * Time: 8:10 AM.
 */

namespace Darkilliant\ImportBundle\Persister;

interface PersisterInterface
{
    public function persist($entity, $batchSize = 20);

    public function finalize();
}
