<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-31
 * Time: 21:58
 */

namespace NemesisPlatform\Game\Form\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\UserCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParticipantType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Season $season */
        $season = $options['season'];

        $categories = [];

        foreach ($season->getLeagues() as $league) {
            foreach ($league->getCategories() as $category) {
                $categories[$league->getName()][$category->getId()] = $category;
            }
        }

        if (count($categories) > 0) {
            $builder->add(
                'category',
                'entity',
                [
                    'choices'     => $categories,
                    'class' => UserCategory::class,
                    'label'       => 'Категория',
                    'attr'        => [
                        'help_text'            => 'Категория участия. Если подходит несколько - выбирайте любую',
                        'data-hider'           => 'category',
                        'data-hider-initiator' => 'true',
                        'style'                => 'horizontal',
                    ],
                    'empty_value' => 'Выберите категорию участия',
                    'required'    => true,
                ]
            );
        }

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
                    'style' => 'normal',
                    'class' => 'col-lg-12',
                ],
                'by_reference' => false,
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

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                /** @var Participant $participant */
                $participant = $event->getData();

                if ($participant) {
                    $form = $event->getForm();

                    $participant->sanitizeValues();

                    $valField = $form->get('values');

                    foreach ($participant->getValues() as $value) {
                        $field = $value->getField();
                        if ($valField->has($field->getName())) {
                            $valField->get($value->getField()->getName())->setData($value);
                        }
                    }
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var \NemesisPlatform\Game\Entity\Participant $participant */
                $participant = $event->getData();
                if ($participant) {
                    $participant->sanitizeValues();
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Participant::class,
                'attr'       => ['style' => 'horizontal'],
            ]
        );
        $resolver->setRequired(['season']);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'participant';
    }
}
