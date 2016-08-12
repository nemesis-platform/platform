<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Mapper;

use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\FormInterface;

final class CallbackDataMapper implements DataMapperInterface
{
    /** @var  callable */
    private $dataToForms;
    /** @var  callable */
    private $formsToData;

    /**
     * CallbackDataMapper constructor.
     *
     * @param callable $dataToForms
     * @param callable $formsToData
     */
    public function __construct(callable $dataToForms, callable $formsToData)
    {
        $this->dataToForms = $dataToForms;
        $this->formsToData = $formsToData;
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param mixed           $data  Structured data
     * @param FormInterface[] $forms A list of {@link FormInterface} instances
     *
     * @throws Exception\UnexpectedTypeException if the type of the data parameter is not supported.
     */
    public function mapDataToForms($data, $forms)
    {
        $callable = $this->dataToForms;

        return $callable($data, $forms);
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[] $forms A list of {@link FormInterface} instances
     * @param mixed           $data  Structured data
     *
     * @throws Exception\UnexpectedTypeException if the type of the data parameter is not supported.
     */
    public function mapFormsToData($forms, &$data)
    {
        $callable = $this->formsToData;

        return $callable($forms, $data);
    }
}
