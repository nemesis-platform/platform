<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.03.2015
 * Time: 15:42
 */

namespace NemesisPlatform\Components\ExportImport\Service;

use NemesisPlatform\Components\ExportImport\PostProcessor\ImportPostProcessorInterface;

interface ImportResultInterface
{
    /** @return mixed The processed data */
    public function getData();

    public function process(ImportPostProcessorInterface $processor);

    /** @return ImportReportInterface */
    public function getReport();
}
