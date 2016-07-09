<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.06.2014
 * Time: 11:52
 */

namespace NemesisPlatform\Admin\Form\Type;

use NemesisPlatform\Core\CMS\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsEntryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', ['label' => 'Заголовок']);
        $builder->add('season', 'site_seasons', ['label' => 'Сезон']);
        $builder->add('imageLink', 'url', ['label' => 'Изображение', 'required' => false]);
        $builder->add('body', 'ckeditor');
        $builder->add(
            'type',
            'choice',
            [
                'choices' => [
                    News::TYPE_DEFAULT  => 'Обычная',
                    News::TYPE_DEFERRED => 'Отложенная',
                    News::TYPE_DISABLED => 'Скрытая',
                ],
                'label'   => 'Тип',
            ]
        );
        $builder->add(
            'date',
            'datetime_local',
            [
                'widget' => 'single_text',
                'label'  => 'Дата',
            ]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => News::class]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'news_entry_type';
    }
}
