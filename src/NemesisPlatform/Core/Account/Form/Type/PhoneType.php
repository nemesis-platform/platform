<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-31
 * Time: 21:58
 */

namespace NemesisPlatform\Core\Account\Form\Type;

use NemesisPlatform\Core\Account\Entity\Phone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PhoneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'phonenumber',
            'text',
            [
                'pattern' => '\d{10}',
                'label'   => 'Номер телефона',
            ]
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => Phone::class]
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'phone_type';
    }
}
