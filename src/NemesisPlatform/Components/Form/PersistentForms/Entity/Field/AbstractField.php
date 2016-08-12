<?php

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Field;

use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\ConfigurableFieldInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\FieldInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\MapperAwareInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\TransformerAwareInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;
use NemesisPlatform\Components\Form\PersistentForms\Entity\ValueInterface;
use NemesisPlatform\Components\Form\PersistentForms\Form\Mapper\CallbackDataMapper;
use NemesisPlatform\Components\Form\PersistentForms\Form\Type\FieldConfigurationType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;

abstract class AbstractField
    implements FormInjectorInterface, FieldInterface, ConfigurableFieldInterface, MapperAwareInterface
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
     * AbstractField constructor.
     *
     * @param string $name
     * @param string $title
     */
    public function __construct($name = null, $title = 'New field')
    {
        $this->name  = $name ?: 'field_'.bin2hex(random_bytes(5));
        $this->title = $title;
    }

    /** {@inheritdoc} */
    public static function create($name = null, $title = null)
    {
        return new static($name, $title);
    }

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
        $field   = $builder->create($this->name, $this->getViewForm(), $options);

        if (get_called_class() instanceof TransformerAwareInterface) {
            $field->addModelTransformer($this->getFormTransformer());
        }

        if (get_called_class() instanceof MapperAwareInterface) {
            $field->setDataMapper($this->getFormMapper());
        }

        $builder->add($field);
    }

    public function getViewFormOptions()
    {
        return [
            'mapped'     => true,
            'empty_data' => null,
            'data_class' => $this->getDataClass(),
            'required'   => $this->isRequired(),
            'label'      => $this->getTitle(),
            'attr'       => ['help_text' => $this->getHelpMessage()],
        ];
    }

    public function getDataClass()
    {
        return AbstractValue::class;
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
     * @return FormTypeInterface|string
     */

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
     * @param string $help
     */
    public function setHelpMessage($help)
    {
        $this->help_message = $help;
    }

    public function getViewForm()
    {
        return FormType::class;
    }

    public function getFormMapper()
    {
        return new CallbackDataMapper(
            function ($data, $forms) {
                /** @var FormInterface @forms */
                dump($data);
                dump($forms);

                if (!$data instanceof ValueInterface) {
                    return;
                }

                $forms->setValue($data->getRawValue());
            },
            function ($forms, &$data) {
                /** @var FormInterface $forms */
                dump($data);
                dump($forms);
                $class = $this->getDataClass();
                /** @var AbstractValue $data */
                $data = new $class($this);
                $data->setValue($forms->getData());
            }
        );
    }

    public function getConfigurationForm()
    {
        return FieldConfigurationType::class;
    }

    public function getConfigurationFormOptions()
    {
        return [
            'data_class' => get_called_class(),
        ];
    }
}
