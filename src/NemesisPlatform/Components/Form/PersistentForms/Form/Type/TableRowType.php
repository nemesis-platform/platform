<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\FieldInterface;
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
            if (!$field instanceof FieldInterface) {
                throw new \LogicException('Option field should contain array of AbstractFields entities');
            }

            $field->injectForm($builder);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('fields');
        $resolver->setAllowedTypes(['fields' => 'array']);
    }
}
