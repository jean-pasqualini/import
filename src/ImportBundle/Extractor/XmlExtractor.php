<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Extractor;

class XmlExtractor
{
    // http://drib.tech/programming/parse-large-xml-files-php
    public function extract(string $filepath, string $nodeName): \Traversable
    {
        $xml = new \XMLReader();

        if (false !== strpos($filepath, '.gz')) {
            $xml->open('compress.zlib://'.$filepath);
        } else {
            $xml->open($filepath);
        }

        $itemCount = 0;
        while ($xml->read()) {
            if ($nodeName != $xml->name) {
                continue;
            }

            ++$itemCount;
            $element = new \SimpleXMLElement($xml->readOuterXml());
            $data = json_decode(json_encode($element), true);

            yield $itemCount => $data;
            $xml->next();
        }
    }
}
