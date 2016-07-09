<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.05.2015
 * Time: 15:27
 */

namespace NemesisPlatform\Components\Form;

use Symfony\Component\Form\FormBuilderInterface;

interface FormInjectorInterface
{
    public function injectForm(FormBuilderInterface $builder);
}
