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
    private $batch = 0;

    private $whiteList = [];
    private $blackList = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function persist($entity, $batchCount = 20, $whiteList = [], $blackList = [])
    {
        ++$this->batch;
        $this->whiteList = $whiteList;
        $this->blackList = $blackList;

        $this->em->persist($entity);

        if ($this->batch >= $batchCount) {
            $this->writeBatch();
        }
    }

    public function finalize()
    {
        $this->writeBatch();
    }

    private function writeBatch()
    {
        $this->em->flush();
        $this->clear();

        $this->batch = 0;
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
