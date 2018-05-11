<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/8/18
 * Time: 3:07 PM.
 */

namespace Darkilliant\ImportBundle\Loader;

class ObjectLoader extends AbstractLoader
{
    public function load($object, array $data, array $options)
    {
        return $this->loadObject($object, $data, $options['mapping'], $options['mapping_relation']);
    }
}
