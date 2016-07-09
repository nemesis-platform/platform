<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 15:36
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\Scenario;

abstract class AbstractScenario
{
    /** @var int|null */
    private $id;
    /** @var  string */
    private $name;
    /** @var  string */
    private $code;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return sprintf("(%s) %s", $this->code, $this->name);
    }

    abstract public function getValue($key);
}
