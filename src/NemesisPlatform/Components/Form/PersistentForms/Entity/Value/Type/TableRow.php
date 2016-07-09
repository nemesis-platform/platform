<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.06.2015
 * Time: 15:17
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type;

use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;

class TableRow
{
    /** @var  int|null */
    private $id;
    /** @var  AbstractValue[]|ArrayCollection */
    private $values;
    /** @var  TableValue */
    private $table;

    /**
     * TableValue constructor.
     *
     * @param TableValue $table
     */
    public function __construct(TableValue $table)
    {
        $this->values = new ArrayCollection();
        $this->table  = $table;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return TableValue
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param TableValue $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return ArrayCollection|AbstractValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param ArrayCollection|AbstractValue[] $values
     */
    public function setValues($values)
    {
        $this->values = new ArrayCollection((array)$values);
    }
}
