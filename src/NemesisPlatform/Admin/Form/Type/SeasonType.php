<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.08.2014
 * Time: 10:23
 */

namespace NemesisPlatform\Admin\Form\Type;

use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Game\Entity\Rule\AbstractRuleEntity;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\SeasonedSite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SeasonType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'site',
            'entity',
            [
                'class'        => SeasonedSite::class,
                'choice_label' => function (SiteInterface $site) {
                    return sprintf('%s (%s)', $site->getFullName(), $site->getDomain());
                },
            ]
        );

        if (array_key_exists('site', $options)) {
            $builder->get('site')->setData($options['site']);
        }

        $builder->add('name', 'text', ['label' => 'Полное название']);
        $builder->add('short_name', 'text', ['label' => 'Короткое название']);
        $builder->add('description', 'textarea', ['label' => 'Описание', 'required' => false]);
        $builder->add(
            'active',
            'checkbox',
            ['required' => false, 'label' => 'Активный', 'attr' => ['align_with_widget' => true]]
        );
        $builder->add(
            'registration_open',
            'checkbox',
            ['required' => false, 'label' => 'Регистрация открыта', 'attr' => ['align_with_widget' => true]]
        );
        $builder->add(
            'leagues',
            'collection',
            [
                'required'     => 'false',
                'label'        => 'Лиги',
                'type'         => new LeagueType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]
        );

        $builder->add(
            'rules',
            'collection',
            [
                'type'         => 'entity',
                'options'      => [
                    'class' => AbstractRuleEntity::class,
                ],
                'required'     => 'false',
                'label'        => 'Логика сезона',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]
        );
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => Season::class,
                    'attr'       => ['style' => 'horizontal'],
                ]
            )
            ->setOptional(
                ['site']
            )
            ->setAllowedTypes(
                ['site' => SeasonedSite::class]
            );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'season';
    }
}
