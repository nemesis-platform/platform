<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.03.2015
 * Time: 15:40
 */

namespace NemesisPlatform\Components\ExportImport\PostProcessor;

use NemesisPlatform\Components\ExportImport\Service\ImportResultInterface;

interface ImportPostProcessorInterface
{
    /**
     * @param ImportResultInterface $result
     *
     * @return bool
     */
    public function isApplicable(ImportResultInterface $result);

    /**
     * @param mixed $data Input data
     *
     * @return mixed Processed data
     */
    public function process($data);
}
