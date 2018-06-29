<?php

declare(strict_types=1);

namespace App\Fake;

use Symfony\Component\Ldap\Adapter\AbstractQuery;

abstract class FakeQuery extends AbstractQuery
{
    public function getArrayResult()
    {
        return [];
    }
}