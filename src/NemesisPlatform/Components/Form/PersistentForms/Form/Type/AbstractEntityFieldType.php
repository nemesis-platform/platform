<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.05.2015
 * Time: 10:10
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

class AbstractEntityFieldType extends AbstractFieldType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('classname', 'text');
    }

}
