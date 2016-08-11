<?php

namespace NemesisPlatform\Components\Form;

use Symfony\Component\Form\FormBuilderInterface;

interface FormInjectorInterface
{
    public function injectForm(FormBuilderInterface $builder, array $options = []);
}
