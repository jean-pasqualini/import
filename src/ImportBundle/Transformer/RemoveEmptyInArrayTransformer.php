<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/11/18
 * Time: 8:51 AM.
 */

namespace Darkilliant\ImportBundle\Transformer;

class RemoveEmptyInArrayTransformer extends AbstractTransformer
{
    public function transform($value, string $name = '', array $options = [])
    {
        return $this->removeEmptyInArray($value);
    }

    public function validate($value, string $name = '', array $options = []): bool
    {
        return true;
    }

    private function removeEmptyInArray($value)
    {
        return array_filter($value, function ($item) {
            return !empty((is_array($item)) ? $this->removeEmptyInArray($item) : $item);
        });
    }
}
