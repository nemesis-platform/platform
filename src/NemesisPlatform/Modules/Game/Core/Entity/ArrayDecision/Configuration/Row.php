<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 11:32
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\ArrayDecision\Configuration;

use NemesisPlatform\Modules\Game\Core\Entity\ArrayDecision\Configuration;

class Row
{
    const TYPE_DATA    = 'data';
    const TYPE_SERVICE = 'service';
    const TYPE_HEADER  = 'header';
    /** @var  int|null */
    private $id;
    /** @var string */
    private $record;
    /** @var string */
    private $type;
    /** @var Configuration */
    private $configuration;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @param string $record
     */
    public function setRecord($record)
    {
        $this->record = $record;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function setType($type)
    {
        if (!in_array($type, [self::TYPE_DATA, self::TYPE_HEADER, self::TYPE_SERVICE])) {
            throw new \InvalidArgumentException('Invalid record type');
        }

        $this->type = $type;
    }

    public function isData()
    {
        return $this->type === self::TYPE_DATA;
    }

    public function isHeader()
    {
        return $this->type === self::TYPE_HEADER;
    }

    public function isService()
    {
        return $this->type === self::TYPE_SERVICE;
    }
}
