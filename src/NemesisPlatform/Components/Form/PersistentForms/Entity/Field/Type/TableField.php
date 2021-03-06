<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.06.2015
 * Time: 13:40
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type;

use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\TableValue;
use NemesisPlatform\Components\Form\PersistentForms\Form\Transformer\ValueTransformer;
use NemesisPlatform\Components\Form\PersistentForms\Form\Type\TableFieldType;
use NemesisPlatform\Components\Form\PersistentForms\Form\Type\TableType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormTypeInterface;

class TableField extends AbstractField
{
    /** @var  ArrayCollection|AbstractField[] */
    private $fields;

    /**
     * TableField constructor.
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|AbstractField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function addField(AbstractField $field)
    {
        if (!$this->fields->contains($field)) {
            $this->fields->set($field->getName(), $field);
        }
    }

    public function removeField(AbstractField $field)
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
        }
    }

    public function getFormType()
    {
        return TableFieldType::class;
    }

    /**
     * @return string|FormTypeInterface
     */
    protected function getRenderedFormType()
    {
        return new TableType($this->fields->toArray());
    }

    /**
     * @return array
     */
    protected function getRenderedFormOptions()
    {
        return array_replace_recursive(
            parent::getRenderedFormOptions(),
            [
                'options' => [
                    'fields' => $this->fields->toArray(),
                ],
            ]
        );

    }

    /**
     * @return DataTransformerInterface
     */
    protected function getValueTransformer()
    {
        $value = new TableValue();
        $value->setField($this);

        return new ValueTransformer($value, 'value');
    }
}
