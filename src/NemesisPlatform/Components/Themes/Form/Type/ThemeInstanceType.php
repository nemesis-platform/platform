<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.01.2015
 * Time: 11:36
 */

namespace NemesisPlatform\Components\Themes\Form\Type;

use NemesisPlatform\Components\Themes\Entity\ThemeInstance;
use NemesisPlatform\Components\Themes\Service\ThemeRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ThemeInstanceType extends AbstractType
{
    /** @var  ThemeRegistry */
    private $registry;

    public function __construct(ThemeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', 'text');

        $themes  = $this->registry->all();
        $choices = array_combine(
            array_keys($themes),
            array_keys($themes)
        );
        $builder->add(
            'theme',
            'choice',
            array('choices' => $choices)
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => ThemeInstance::class));
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'switchable_theme_instance';
    }
}
