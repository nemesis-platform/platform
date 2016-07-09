<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.03.2015
 * Time: 15:31
 */

namespace NemesisPlatform\Components\ExportImport\Service;

use NemesisPlatform\Components\ExportImport\PostProcessor\ImportPostProcessorInterface;

interface ImporterInterface
{
    /** @param mixed $data */
    public function setup($data);

    /** @return bool */
    public function isValid();

    /** @return ImportResultInterface */
    public function getResult();

    /** @param ImportPostProcessorInterface $processor */
    public function registerPostProcessor(ImportPostProcessorInterface $processor);

    /** @return ImportPostProcessorInterface[] */
    public function getPostProcessors();
}
