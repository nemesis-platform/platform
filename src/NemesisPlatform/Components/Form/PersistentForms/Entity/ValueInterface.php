<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Entity;

interface ValueInterface
{
    /** @return FieldInterface */
    public function getField();

    /** @return mixed */
    public function getRawValue();

    /** @return mixed */
    public function getRenderedValue();
}
