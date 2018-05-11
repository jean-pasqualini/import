<?php

namespace Darkilliant\ImportBundle\Registry;

use Darkilliant\ImportBundle\Transformer\TransformerInterface;

/**
 * @internal
 * Class TransformerRegistry
 */
class TransformerRegistry
{
    /** @var TransformerInterface[] */
    private $collection;

    public function get($id): TransformerInterface
    {
        return $this->collection[$id];
    }

    public function add($id, TransformerInterface $transformer)
    {
        $this->collection[$id] = $transformer;
    }
}
