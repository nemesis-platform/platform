<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 07.07.2015
 * Time: 14:48
 */

namespace NemesisPlatform\Admin\Form\Type\Block;

use Symfony\Component\Form\FormBuilderInterface;

class CustomContentBlockType extends AbstractBlockType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('content', 'ckeditor');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'custom_content_block';
    }
}
