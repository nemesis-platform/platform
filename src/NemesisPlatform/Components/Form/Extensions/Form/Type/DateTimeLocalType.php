<?php

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
