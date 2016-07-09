<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 04.03.2015
 * Time: 9:55
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\ArrayDecision;

use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Modules\Game\Core\Entity\Decision;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class ArrayDecision extends Decision
{
    /** @var  ArrayDecisionDataRecord[]|ArrayCollection */
    protected $data;

    public function __construct(Team $team, UserInterface $author)
    {
        parent::__construct($team, $author);
        $this->data = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|ArrayDecisionDataRecord[]
     */
    public function getData()
    {
        return $this->data;
    }

    public function addData(ArrayDecisionDataRecord $record)
    {
        $this->offsetUnset($record->getKey());
        if ($record->getValue() !== null) {
            $this->data->add($record);
            $record->attach($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        foreach ($this->data as $record) {
            if ($record->getKey() === $offset) {
                $this->data->removeElement($record);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        foreach ($this->data as $record) {
            if ($record->getKey() === $offset) {
                return true;
            }
        }

        return false;
    }

    public function removeData(ArrayDecisionDataRecord $record)
    {
        $this->offsetUnset($record->getKey());
    }

    public function getArrayData()
    {
        $data = [];
        foreach ($this->data as $record) {
            $data[$record->getKey()] = $record->getValue();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $record = $this->offsetGet($offset);

        if ($record !== null) {
            $this->offsetUnset($offset);
        }

        if ($value !== null) {
            $record = new ArrayDecisionDataRecord($this, $offset, $value);
            $this->data->add($record);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        foreach ($this->data as $record) {
            if ($record->getKey() === $offset) {
                return $record->getValue();
            }
        }

        return null;
    }
}
