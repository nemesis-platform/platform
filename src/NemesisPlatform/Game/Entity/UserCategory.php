<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-09-28
 * Time: 12:37
 */

namespace NemesisPlatform\Game\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;

class UserCategory
{
    /** @var  int|null */
    private $id;
    /** @var  League|null */
    private $league;
    /** @var  string */
    private $name;
    /** @var  ArrayCollection|AbstractField[] */
    private $fields;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|AbstractField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param ArrayCollection|AbstractField[] $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return League|null
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * @param League|null $league
     */
    public function setLeague($league)
    {
        $this->league = $league;
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

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
    }
}
