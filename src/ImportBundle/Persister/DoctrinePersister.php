<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/8/18
 * Time: 7:56 AM.
 */

namespace Darkilliant\ImportBundle\Persister;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @internal
 */
class DoctrinePersister implements PersisterInterface
{
    private $em;
    private $batch = [];

    private $whiteList = [];
    private $blackList = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function persist($entity, $batchCount = 20, $whiteList = [], $blackList = [])
    {
        $this->batch[] = $entity;
        $this->whiteList = $whiteList;
        $this->blackList = $blackList;

        if (count($this->batch) >= $batchCount) {
            $this->writeBatch();
        }
    }

    public function finalize()
    {
        $this->writeBatch();
    }

    private function writeBatch()
    {
        foreach ($this->batch as $item) {
            $this->em->persist($item);
        }

        $this->em->flush();
        $this->clear();

        $this->batch = [];
    }

    private function clear()
    {
        if (empty($this->whiteList) && empty($this->blackList)) {
            $this->em->clear();
        } elseif (!empty($this->whiteList)) {
            foreach ($this->whiteList as $class) {
                $this->em->clear($class);
            }
        } elseif (!empty($this->blackList)) {
            throw new \Exception('unsupported blacklist');
        }

        gc_collect_cycles();
    }
}
