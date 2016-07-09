<?php

namespace NemesisPlatform\Components\Form;

use Symfony\Component\Form\FormTypeInterface;

interface FormTypedInterface
{
    /**
     * @return FormTypeInterface|string FormTypeInterface instance or string which represents registered form type
     */
    public function getFormType();
}
