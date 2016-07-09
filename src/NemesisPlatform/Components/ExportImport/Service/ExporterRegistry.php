<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.10.2014
 * Time: 11:04
 */

namespace NemesisPlatform\Components\ExportImport\Service;

class ExporterRegistry
{
    /** @var  ExporterInterface[] */
    private $exporters = [];


    /** @return ExporterInterface[] */
    public function all()
    {
        return $this->exporters;
    }
    
}
