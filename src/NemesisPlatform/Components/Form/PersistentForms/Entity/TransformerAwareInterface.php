<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Entity;

use Symfony\Component\Form\DataTransformerInterface;

interface TransformerAwareInterface
{
    /**
     * @return DataTransformerInterface
     */
    public function getFormTransformer();
}
