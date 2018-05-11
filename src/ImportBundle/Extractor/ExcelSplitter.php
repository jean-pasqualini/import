<?php

namespace Darkilliant\ImportBundle\Extractor;

use Cocur\Slugify\Slugify;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;

class ExcelSplitter
{
    /** @var Slugify */
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    /**
     * @param $filePath
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return array
     */
    public function split($filePath): array
    {
        $files = [];

        $xlsFilePath = realpath($filePath);
        $xlsWorkSheet = IOFactory::load($filePath);
        /** @var IWriter $csvWriter */
        $csvWriter = IOFactory::createWriter($xlsWorkSheet, 'Csv');
        foreach ($xlsWorkSheet->getWorksheetIterator() as $sheetIndex => $workSheetTab) {
            $csvWriter->setSheetIndex($sheetIndex);

            $sheetName = strtolower($this->slugify->slugify($workSheetTab->getTitle()));
            $filePath = sprintf(
                '%s_%s.csv',
                pathinfo($xlsFilePath, PATHINFO_DIRNAME).'/'.pathinfo($xlsFilePath, PATHINFO_FILENAME),
                $sheetName
            );

            $csvWriter->save($filePath);

            $files[] = ['sheet_name' => $sheetName, 'filepath' => $filePath];
        }

        return $files;
    }
}
