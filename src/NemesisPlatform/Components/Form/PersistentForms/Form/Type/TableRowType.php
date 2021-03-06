<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.06.2015
 * Time: 14:08
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableRowType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'field_table_row';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var AbstractField[] $fields */
        $fields = $options['fields'];

        foreach ($fields as $field) {
            if (!$field instanceof AbstractField) {
                throw new \LogicException('Option field should contain array of AbstractFields entities');
            }

            $field->buildForm($builder);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('fields');
        $resolver->setAllowedTypes(['fields' => 'array']);
    }
}
