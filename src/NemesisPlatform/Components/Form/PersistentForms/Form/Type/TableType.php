<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.06.2015
 * Time: 14:07
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\TableValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TableType extends AbstractType
{
    public function getParent()
    {
        return 'collection';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->setDefaultOptions($resolver);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'type'         => 'field_table_row',
                'allow_add'    => true,
                'allow_delete' => true,
            )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($options) {
                /** @var TableValue $tableValue */
                $tableValue = $event->getData();
                if (!$tableValue) {
                    return;
                }
                $form = $event->getForm();
                foreach ($tableValue->getValue() as $key => $row) {
                    $form->add($key,$options['type'], $options['options']);
                    $form->get($key)->setData($row);
                }
            }
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'field_table';
    }
}
