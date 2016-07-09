<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.05.2014
 * Time: 11:45
 */

namespace NemesisPlatform\Admin\Form\Type;

use NemesisPlatform\Core\CMS\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', ['label' => 'Название меню']);
        $builder->add('site', 'current_site');

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Menu $menu */
                $menu = $event->getData();

                $form = $event->getForm();

                if ($menu && $menu->getId()) {
                    $form->add(
                        'elements',
                        'collection',
                        [
                            'label'              => 'Элементы',
                            'type'               => 'menu_element_type',
                            'allow_add'          => true,
                            'allow_delete'       => true,
                            'by_reference'       => false,
                            'attr'               => ['data-nested-sortable' => true],
                        ]
                    );
                }
            }
        );
    }

    /** {@inheritdoc} */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'empty_data' => function (FormInterface $form) {
                    return new Menu(
                        $form->get('name')->getData(),
                        $form->get('site')->getData()
                    );
                },
                'data_class' => Menu::class,
            ]
        );
    }

    /** {@inheritdoc} */
    public function getName()
    {
        return 'menu_type';
    }
}
