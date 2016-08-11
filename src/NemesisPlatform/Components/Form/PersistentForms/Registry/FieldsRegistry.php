<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 29.09.2014
 * Time: 13:51
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Registry;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\FieldInterface;

class FieldsRegistry
{
    /** @var FieldInterface[] */
    private $types = [];

    /**
     * @param string $alias
     *
     * @return FieldInterface
     */
    public function get($alias)
    {
        return $this->types[$alias];
    }

    /**
     * @param string         $alias
     * @param FieldInterface $field
     */
    public function add($alias, FieldInterface $field)
    {
        $this->types[$alias] = $field;
    }

    /**
     * @return FieldInterface[]
     */
    public function all()
    {
        return $this->types;
    }
}
