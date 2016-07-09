<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-09-29
 * Time: 22:18
 */

namespace NemesisPlatform\Game\Form\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Registry\FieldsRegistry;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Season;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileFormType extends AbstractType
{
    /** @var  FieldsRegistry */
    private $formRegistry;

    /**
     * @param $formRegistry
     */
    public function __construct(FieldsRegistry $formRegistry)
    {
        $this->formRegistry = $formRegistry;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Season $season */
        $season = $options['season'];

        /** @var AbstractField[] $fields */
        $fields = [];

        foreach ($season->getLeagues() as $league) {
            foreach ($league->getCategories() as $category) {
                foreach ($category->getFields() as $field) {
                    $fields[] = $field;
                }
            }
        }

        $values = $builder->create(
            'values',
            'form',
            [
                'label' => false,
                'attr'  => [
                    'style'      => 'horizontal',
                    'widget_col' => 12,
                    'class'      => 'row',
                ],
            ]
        );

        foreach ($fields as $field) {
            $cats = [];

            foreach ($season->getLeagues() as $league) {
                foreach ($league->getCategories() as $category) {
                    foreach ($category->getFields() as $field1) {
                        if ($field1 === $field) {
                            $cats[] = $category->getId();
                        }
                    }
                }
            }

            $options = [
                'attr'     => [
                    'data-hider'         => 'category',
                    'data-hider-sources' => implode(',', $cats),
                    'style'              => 'horizontal',
                ],
                'required' => true,
            ];

            $field->buildForm($values, $options);
        }

        $builder->add($values);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['season']);
        $resolver->setDefaults(
            [
                'data_class' => Participant::class,
                'label'      => false,
                'attr'       => ['style' => 'horizontal'],
            ]
        );
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'profile_type';
    }
}
