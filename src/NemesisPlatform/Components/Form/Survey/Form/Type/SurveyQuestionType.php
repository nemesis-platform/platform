<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 10.06.2015
 * Time: 14:05
 */

namespace NemesisPlatform\Components\Form\Survey\Form\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\FieldInterface;
use NemesisPlatform\Components\Form\Survey\Entity\SurveyQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyQuestionType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'survey_question';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'field',
            'entity',
            [
                'label'        => 'Поле',
                'class'        => AbstractField::class,
                'choice_label' => function (FieldInterface $field) {
                    return sprintf('%s [%s]', $field->getTitle(), $field->getName());
                },
            ]
        );
        $builder->add('weight', 'integer', ['label' => 'Вес']);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => SurveyQuestion::class]);
    }
}
