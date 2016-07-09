<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.02.2015
 * Time: 13:18
 */

namespace NemesisPlatform\Admin\Service\Generator;

interface EntityGeneratorInterface
{
    /**
     * @param array $data
     *
     * @return GenerationReportInterface
     */
    public function generate($data = []);
}
