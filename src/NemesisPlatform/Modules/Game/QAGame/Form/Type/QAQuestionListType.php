<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.06.2015
 * Time: 17:45
 */

namespace NemesisPlatform\Modules\Game\QAGame\Form\Type;

use NemesisPlatform\Modules\Game\QAGame\Entity\QuestionList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QAQuestionListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add(
            'questions',
            'collection',
            [
                'type'         => 'module_qa_game_question',
                'options'      => ['attr' => ['style' => 'inline']],
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => QuestionList::class,
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
        return 'module_qa_game_question_list';
    }
}
