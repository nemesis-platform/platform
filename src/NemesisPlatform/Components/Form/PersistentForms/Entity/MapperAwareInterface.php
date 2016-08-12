<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Entity;

use Symfony\Component\Form\DataMapperInterface;

interface MapperAwareInterface
{
    /**
     * @return DataMapperInterface
     */
    public function getFormMapper();

    /**
     * @return string FQCN
     */
    public function getDataClass();
}
