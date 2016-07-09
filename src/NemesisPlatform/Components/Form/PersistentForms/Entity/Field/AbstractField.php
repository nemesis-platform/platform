<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 29.09.2014
 * Time: 14:01
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Field;


use NemesisPlatform\Components\Form\FormTypedInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\PlainValue;
use NemesisPlatform\Components\Form\PersistentForms\Form\Transformer\ValueTransformer;
use NemesisPlatform\Components\Form\PersistentForms\Form\Type\AbstractFieldType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;

abstract class AbstractField implements FormTypedInterface
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

    public function __toString()
    {
        return sprintf('"%s" %s', $this->getName(), $this->getType());
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

    /**
     * @return FormTypeInterface|string FormTypeInterface instance or string which represents registered form type
     */
    public function getFormType()
    {
        return new AbstractFieldType(get_class($this));
    }

    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $options = array_replace_recursive(
            array(
                'required' => $this->isRequired(),
                'label'    => $this->getTitle(),
                'attr'     => array('help_text' => $this->getHelpMessage()),
            ),
            $this->getRenderedFormOptions(),
            $options
        );

        $field = $builder->create($this->name, $this->getRenderedFormType(), $options);

        if ($this->getValueTransformer()) {
            $field->addModelTransformer($this->getValueTransformer());
        }

        $builder->add($field);
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
        $this->required = $required;
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
     * @return string
     */
    public function getHelpMessage()
    {
        return $this->help_message;
    }

    /**
     * @return FormTypeInterface|string
     */

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
        return array();
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
}
