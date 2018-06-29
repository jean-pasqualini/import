<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Registry;

use Darkilliant\ProcessBundle\Filter\AbstractFilter;

class FilterRegistry
{
    /** @var AbstractFilter[] */
    private $collection;

    public function get($id): AbstractFilter
    {
        return $this->collection[$id];
    }

    public function add($id, AbstractFilter $transformer)
    {
        $this->collection[$id] = $transformer;
    }
}
