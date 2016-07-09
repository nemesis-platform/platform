<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.03.2015
 * Time: 16:53
 */

namespace NemesisPlatform\Components\ExportImport\Service;

use NemesisPlatform\Components\ExportImport\PostProcessor\ImportPostProcessorInterface;

class SimpleResult implements ImportResultInterface
{
    private $data;

    private $processors = array();

    /**
     * SimpleResult constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }


    /** @return mixed The processed data */
    public function getData()
    {
        return $this->data;
    }

    public function process(ImportPostProcessorInterface $processor)
    {
        $processor->process($this->data);
        $this->processors[] = $processor;
    }

    /** @return ImportReportInterface */
    public function getReport()
    {
        return new SimpleReport(count($this->data), $this->processors);
    }
}
