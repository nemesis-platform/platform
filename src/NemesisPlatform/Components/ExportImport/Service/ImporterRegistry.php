<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.03.2015
 * Time: 15:33
 */

namespace NemesisPlatform\Components\ExportImport\Service;

class ImporterRegistry
{
    /** @var  ImporterInterface[] */
    private $importers = array();

    /** @return ImporterInterface[] */
    public function all()
    {
        return $this->importers;
    }
}
