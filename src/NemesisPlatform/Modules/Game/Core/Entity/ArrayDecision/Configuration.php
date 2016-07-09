<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 11:32
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\ArrayDecision;

class Configuration
{
    /** @var  Configuration\Row */
    protected $rows;
    /** @var  int|null */
    private $id;

    /**
     * @return Configuration\Row
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param Configuration\Row $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }
}
