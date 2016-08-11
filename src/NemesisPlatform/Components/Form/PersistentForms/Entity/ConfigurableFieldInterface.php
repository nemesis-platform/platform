<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Entity;

interface ConfigurableFieldInterface
{
    /** @return string FQCN of CONFIGURATION form */
    public function getConfigurationForm();

    /** @return array options to configure CONFIGURATION form */
    public function getConfigurationFormOptions();
}
