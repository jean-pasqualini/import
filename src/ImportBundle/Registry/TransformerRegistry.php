<?php

namespace Darkilliant\ImportBundle\Registry;

use Darkilliant\ImportBundle\Transformer\AbstractTransformer;

/**
 * @internal
 * Class TransformerRegistry
 */
class TransformerRegistry
{
    /** @var TransformerInterface[] */
    private $collection;

    public function get($id): AbstractTransformer
    {
        return $this->collection[$id];
    }

    public function add($id, AbstractTransformer $transformer)
    {
        $this->collection[$id] = $transformer;
    }
}
