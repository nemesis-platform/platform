<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.06.2015
 * Time: 17:25
 */

namespace NemesisPlatform\Modules\Game\QAGame\Form\Type;

use NemesisPlatform\Modules\Game\Core\Form\Type\RoundType;
use NemesisPlatform\Modules\Game\QAGame\Entity\QARound;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QARoundType extends RoundType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('start', 'date', ['widget' => 'single_text']);
        $builder->add('finish', 'date', ['widget' => 'single_text']);
        $builder->add(
            'questionList',
            null,
            [
                'label'    => 'Опросный лист',
                'required' => true,
            ]
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => QARound::class,
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
        return 'module_qa_game_round';
    }
}
