<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 25.05.2015
 * Time: 13:18
 */

namespace NemesisPlatform\Components\Form\Survey\Form\Type;

use NemesisPlatform\Components\Form\Survey\Entity\Survey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyFormType extends AbstractType
{

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'survey_form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alias');
        $builder->add('title');
        $builder->add('editAllowed', null, ['required' => false]);
        $builder->add('locked', null, ['required' => false]);
        $builder->add('public', null, ['required' => false]);

        $builder->add(
            'questions',
            'collection',
            [
                'type'         => new SurveyQuestionType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options'      => [
                    'attr' => ['style' => 'inline'],
                ],
            ]
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => Survey::class]);
    }
}
