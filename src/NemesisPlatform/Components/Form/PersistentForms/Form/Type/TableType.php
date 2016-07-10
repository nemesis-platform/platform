<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.06.2015
 * Time: 14:07
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Form\Type;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\TableValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableType extends AbstractType
{
    /** @var  AbstractField[] */
    private $fields;

    /**
     * TableType constructor.
     *
     * @param AbstractField[] $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function getParent()
    {
        return CollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'type'         => TableRowType::class,
                'options'      => [
                    'fields' => $this->fields,
                ],
                'allow_add'    => true,
                'allow_delete' => true,
            ]
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($options) {
                /** @var TableValue $tableValue */
                $tableValue = $event->getData();
                if (!$tableValue) {
                    return;
                }
                $form = $event->getForm();
                foreach ($tableValue->getValue() as $key => $row) {
                    $form->add($key, $options['type'], $options['options']);
                    $form->get($key)->setData($row);
                }
            }
        );
    }
}
