<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Field;

use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\Form\FormTypedInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\FieldInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\PlainValue;
use NemesisPlatform\Components\Form\PersistentForms\Form\Transformer\ValueTransformer;
use NemesisPlatform\Components\Form\PersistentForms\Form\Type\AbstractFieldType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;

abstract class AbstractField implements FormTypedInterface, FieldInterface, FormInjectorInterface
{
    /** @var  int|null */
    private $id;
    /** @var  string */
    private $name;
    /** @var  string */
    private $title;
    /** @var  string */
    private $help_message;
    /** @var  bool */
    private $required = true;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function injectForm(FormBuilderInterface $builder, array $options = [])
    {
        $options = array_replace_recursive($this->getViewFormOptions(), $options);
        $field   = $builder->create($this->name, $this->getRenderedFormType(), $options);

        if (null !== ($transformer = $this->getValueTransformer())) {
            $field->addModelTransformer($transformer);
        }

        $builder->add($field);
    }

    public function getViewFormOptions()
    {
        return array_replace_recursive(
            [
                'required' => $this->isRequired(),
                'label'    => $this->getTitle(),
                'attr'     => ['help_text' => $this->getHelpMessage()],
            ],
            $this->getRenderedFormOptions()
        );
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = (bool)$required;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return FormTypeInterface|string
     */

    /**
     * @return string
     */
    public function getHelpMessage()
    {
        return $this->help_message;
    }

    /**
     * @param string $help
     */
    public function setHelpMessage($help)
    {
        $this->help_message = $help;
    }

    /**
     * @return array
     */
    protected function getRenderedFormOptions()
    {
        return [];
    }

    /**
     * @return string|FormTypeInterface
     */
    abstract protected function getRenderedFormType();

    /**
     * @return DataTransformerInterface
     */
    protected function getValueTransformer()
    {
        $value = new PlainValue();
        $value->setField($this);

        return new ValueTransformer($value, 'value');
    }

    public function getViewForm()
    {
        return $this->getFormType();
    }

    /**
     * @return FormTypeInterface|string FormTypeInterface instance or string which represents registered form type
     */
    public function getFormType()
    {
        return AbstractFieldType::class;
    }
}
