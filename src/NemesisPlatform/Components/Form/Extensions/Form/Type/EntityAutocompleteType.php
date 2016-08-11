<?php

namespace NemesisPlatform\Components\Form\Extensions\Form\Type;

use NemesisPlatform\Components\Form\Extensions\Form\DataTransformer\EntityToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityAutocompleteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new EntityToArrayTransformer(
                $options['class'],
                $options['visible_property_path']
            )
        );

        $attrs = ['data-autocomplete' => $options['action']];
        if ($options['autocomplete_group']) {
            $attrs['data-autocomplete-group'] = $options['autocomplete_group'];
        }
        if ($options['autocomplete_term']) {
            $attrs['data-autocomplete-term'] = $options['autocomplete_term'];
        }
        if ($options['autocomplete_label']) {
            $attrs['data-autocomplete-label'] = $options['autocomplete_label'];
        }
        if ($options['autocomplete_id']) {
            $attrs['data-autocomplete-id'] = $options['autocomplete_id'];
        }

        $builder->add(
            'helper',
            'text',
            [
                'label'      => $options['label'],
                'label_attr' => array_merge($options['label_attr']),
                'attr'       => array_merge(
                    $options['attr'],
                    $attrs
                ),
            ]
        );
        $builder->add('storage', 'entity_hidden', ['class' => $options['class']]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(
                [
                    'class',
                    'action',
                    'visible_property_path',
                ]
            )
            ->setOptional(
                [
                    'autocomplete_group',
                    'autocomplete_term',
                    'autocomplete_label',
                    'autocomplete_id',
                ]
            )
            ->setDefaults(
                [
                    'compound'           => true,
                    'invalid_message'    => 'The entity does not exist.',
                    'autocomplete_group' => null,
                    'autocomplete_term'  => null,
                    'autocomplete_label' => null,
                    'autocomplete_id'    => null,
                ]
            );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['label']      = false;
        $view->vars['attr']       = [];
        $view->vars['label_attr'] = [];
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'entity_autocomplete';
    }
}
