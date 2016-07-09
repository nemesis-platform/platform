<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 11.03.2015
 * Time: 15:27
 */

namespace NemesisPlatform\Admin\Form\Type;

use Symfony\Component\Form\AbstractType;

class JasnyFileInputType extends AbstractType
{
    public function getParent()
    {
        return 'file';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'jasny_fileinput';
    }
}
