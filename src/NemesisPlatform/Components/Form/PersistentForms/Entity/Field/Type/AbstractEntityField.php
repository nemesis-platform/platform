<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.05.2015
 * Time: 10:09
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Form\Type\AbstractEntityFieldType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormTypeInterface;

abstract class AbstractEntityField extends AbstractField
{
    /** @return string */
    abstract public function getClassname();

    abstract public function renderEntity($entity);

    public function getFormType()
    {
        return new AbstractEntityFieldType(get_class($this));
    }

    /**
     * @return string|FormTypeInterface
     */
    protected function getRenderedFormType()
    {
        return EntityType::class;
    }
}
