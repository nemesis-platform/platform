<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.02.2015
 * Time: 13:25
 */

namespace NemesisPlatform\Admin\Service\Generator;

class GenerationReport implements GenerationReportInterface
{
    /** @var  array */
    private $data = [];
    /** @var  string[] */
    private $notifications = [];

    /**
     * @param $row mixed
     */
    public function addDataRow($row)
    {
        $this->data[] = $row;
    }

    /** @return array */
    public function getData()
    {
        return $this->data;
    }

    /** @return array */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param $notification string
     */
    public function addNotification($notification)
    {
        $this->notifications[] = $notification;
    }
}
