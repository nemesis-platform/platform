<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-31
 * Time: 21:58
 */

namespace NemesisPlatform\Core\Account\Form\Type;

use NemesisPlatform\Core\Account\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserAdditionalDataType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('about', 'textarea', ['required' => false, 'label' => 'О себе']);
        $builder->add('social_facebook', 'text', ['required' => false, 'label' => 'Facebook ID']);
        $builder->add('social_twitter', 'text', ['required' => false, 'label' => 'Twitter ID']);
        $builder->add('social_vkontakte', 'text', ['required' => false, 'label' => 'VKontakte ID']);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_info_type';
    }
}
