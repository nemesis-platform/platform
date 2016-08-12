<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 29.09.2014
 * Time: 13:51
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Registry;

use NemesisPlatform\Components\Form\PersistentForms\Entity\FieldInterface;

class FieldsRegistry
{
    /** @var FieldInterface[] */
    private $types = [];

    /**
     * @param string $alias
     *
     * @return FieldInterface
     * @throws \OutOfBoundsException
     */
    public function get($alias)
    {
        if (!$this->has($alias)) {
            throw new \OutOfBoundsException('No type '.$alias.' registered');
        }

        return $this->types[$alias];
    }

    public function has($alias)
    {
        return array_key_exists($alias, $this->types);
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
