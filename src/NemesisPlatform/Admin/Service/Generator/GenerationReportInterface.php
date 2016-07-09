<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.02.2015
 * Time: 13:21
 */

namespace NemesisPlatform\Admin\Service\Generator;

interface GenerationReportInterface
{
    /** @return array */
    public function getData();

    /** @return array */
    public function getNotifications();
}
