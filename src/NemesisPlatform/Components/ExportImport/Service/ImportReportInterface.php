<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.03.2015
 * Time: 15:49
 */

namespace NemesisPlatform\Components\ExportImport\Service;

use NemesisPlatform\Components\ExportImport\PostProcessor\ImportPostProcessorInterface;

interface ImportReportInterface
{
    /** @return ImportPostProcessorInterface[] */
    public function getAppliedProcessors();

    /** @return array Summary Header-Value Table */
    public function getSummaryTable();
}
