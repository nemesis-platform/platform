<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.03.2015
 * Time: 16:54
 */

namespace NemesisPlatform\Components\ExportImport\Service;

use NemesisPlatform\Components\ExportImport\PostProcessor\ImportPostProcessorInterface;

class SimpleReport implements ImportReportInterface
{
    private $count = 0;
    private $processors = array();

    /**
     * SimpleReport constructor.
     *
     * @param int   $count
     * @param array $processors
     */
    public function __construct($count, array $processors)
    {
        $this->count      = $count;
        $this->processors = $processors;
    }

    /** @return ImportPostProcessorInterface[] */
    public function getAppliedProcessors()
    {
        return $this->processors;
    }

    /** @return array Summary Header-Value Table */
    public function getSummaryTable()
    {
        return array(array('count' => $this->count));
    }
}
