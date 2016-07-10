<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 23.07.2014
 * Time: 12:16
 */

namespace NemesisPlatform\Components\Form\Extensions\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTimeLocalType extends AbstractType
{


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'widget' => 'single_text',
                    'format' => "yyyy-MM-dd'T'HH:mm",
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
        return 'datetime_local';
    }

    public function getParent()
    {
        return 'datetime';
    }
}
