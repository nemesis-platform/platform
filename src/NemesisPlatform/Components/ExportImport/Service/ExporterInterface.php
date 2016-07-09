<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.10.2014
 * Time: 11:21
 */

namespace NemesisPlatform\Components\ExportImport\Service;

use Symfony\Component\HttpFoundation\Response;

interface ExporterInterface
{
    /**
     * @param array $options
     *
     * @return Response
     */
    public function export(array $options = array());
}
