<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Entity;

interface FieldInterface
{
    /** @return string FQCN of VIEW form class */
    public function getViewForm();

    /** @return array options to configure VIEW form */
    public function getViewFormOptions();
}
