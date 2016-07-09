<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 29.09.2014
 * Time: 13:51
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Registry;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;

class FieldsRegistry
{
    /** @var AbstractField[] */
    private $types = [];

    /**
     * @param string $alias
     *
     * @return AbstractField
     */
    public function get($alias)
    {
        return $this->types[$alias];
    }

    /**
     * @param string        $alias
     * @param AbstractField $field
     */
    public function add($alias, AbstractField $field)
    {
        $this->types[$alias] = $field;
    }

    /**
     * @return AbstractField[]
     */
    public function all()
    {
        return $this->types;
    }
}
