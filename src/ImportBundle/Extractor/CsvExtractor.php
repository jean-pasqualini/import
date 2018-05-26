<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Extractor;

use Cocur\Slugify\Slugify;

/**
 * @internal
 */
class CsvExtractor implements ExtractorInterface
{
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function extract(string $csvFilePath, string $delimiter = ',', array $columsNames = null): \Traversable
    {
        $csvFileObjects = new \SplFileObject($csvFilePath);
        $csvFileObjects->setFlags((\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE | \ SplFileObject::READ_AHEAD));
        $csvFileObjects->setCsvControl($delimiter);

        $arrayKeys = array_map([$this, 'slugify'], $columsNames ?? $csvFileObjects->current());
        $arrayKeys = array_map('trim', $arrayKeys);

        foreach ($csvFileObjects as $loopIndex => $csvFileObjectRow) {
            if ($csvFileObjects->key() > 0 && true === $this->isValidLine($csvFileObjectRow)) {
                $csvFileObjectRow = array_map('trim', $csvFileObjectRow);
                $arrayToYield = array_combine($arrayKeys, $csvFileObjectRow);
                yield $loopIndex => $arrayToYield;
            }
        }
    }

    private function slugify($key)
    {
        $key = str_replace('/', '_', $key);

        return $this->slugify->slugify($key, '_');
    }

    private function isValidLine(array $csvFileObjectRow): bool
    {
        return (count(array_keys($csvFileObjectRow, '')) === count($csvFileObjectRow)) ? false : true;
    }
}
