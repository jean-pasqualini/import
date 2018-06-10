<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Filter;

use Darkilliant\ProcessBundle\Registry\FilterRegistry;

class MappingFilter
{
    /** @var FilterRegistry */
    private $registry;

    public function __construct(FilterRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function isAccept(array $filters)
    {
        foreach ($filters as $filterConfig) {
            $localAccept = $this->registry->get($filterConfig['type'])
                ->isAccept($filterConfig['value'], $filterConfig['options']);

            $validWhenReturn = $filterConfig['valid_when_return'] ?? true;

            if ($localAccept !== $validWhenReturn) {
                return false;
            }
        }

        return true;
    }
}
