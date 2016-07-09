<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 03.07.2015
 * Time: 12:58
 */

namespace NemesisPlatform\Core\CMS\Entity\Block;

use NemesisPlatform\Admin\Form\Type\Block\AbstractBlockType;
use NemesisPlatform\Components\Form\FormTypedInterface;

abstract class AbstractBlock implements BlockInterface, FormTypedInterface
{
    /** @var  int|null */
    private $id;
    /** @var  string */
    private $description;

    public function __toString()
    {
        return $this->getDescription().' : '.static::class;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getFormType()
    {
        return new AbstractBlockType(get_class($this));
    }
}
