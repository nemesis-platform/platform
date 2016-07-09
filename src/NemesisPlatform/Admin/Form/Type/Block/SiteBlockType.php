<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 03.07.2015
 * Time: 14:57
 */

namespace NemesisPlatform\Admin\Form\Type\Block;

use NemesisPlatform\Core\CMS\Entity\Block\SiteBlock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('block', null, ['required' => true]);
        if (count($options['areas']) === 0) {
            $builder->add('area', 'text');
        } else {
            $builder->add('area', 'choice', ['choices' => (array)$options['areas']]);
        }
        $builder->add('weight');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'site_block';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => SiteBlock::class,
                'attr' => ['style' => 'inline'],
                'areas' => [],
            ]
        );
        $resolver->setOptional(['areas']);
    }
}
