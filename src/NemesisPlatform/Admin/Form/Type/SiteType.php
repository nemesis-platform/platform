<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.08.2014
 * Time: 10:23
 */

namespace NemesisPlatform\Admin\Form\Type;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Admin\Form\Type\Block\SiteBlockType;
use NemesisPlatform\Components\Themes\Service\ThemeInterface;
use NemesisPlatform\Components\Themes\Service\ThemeRegistry;
use NemesisPlatform\Core\CMS\Entity\Block\AreaProviderInterface;
use NemesisPlatform\Game\Entity\SeasonedSite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteType extends AbstractType
{
    /** @var  ThemeRegistry */
    private $themeRegistry;

    public function __construct(ThemeRegistry $themeRegistry)
    {
        $this->themeRegistry = $themeRegistry;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('full_name', 'text', ['required' => true, 'label' => 'Название']);
        $builder->add('short_name', 'text', ['required' => true, 'label' => 'Короткое название']);
        $builder->add(
            'active',
            'checkbox',
            ['required' => false, 'label' => 'Активный', 'attr' => ['align_with_widget' => true]]
        );
        $builder->add('base_url', 'text', ['required' => true, 'label' => 'Хост']);
        $builder->add('support_email', 'email', ['required' => true, 'label' => 'Контактный адрес']);
        $builder->add('logo_url', 'text', ['required' => false, 'label' => 'Логотип']);
        $builder->add(
            'theme',
            'choice',
            [
                'choices' =>
                    array_combine(
                        array_keys($this->themeRegistry->all()),
                        array_map(
                            function (ThemeInterface $theme) {
                                return 'theme.'.$theme->getType();
                            },
                            $this->themeRegistry->all()
                        )
                    ),
                'label' => 'Оформление',
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                /** @var SeasonedSite $site */
                $site = $event->getData();

                if ($site && $site->getId()) {
                    $form->add(
                        'themeInstance',
                        null,
                        [
                            'query_builder' => function (EntityRepository $repository) use ($site) {
                                return
                                    $repository
                                        ->createQueryBuilder('i')
                                        ->andWhere('i.theme = :theme')
                                        ->setParameter('theme', $site->getTheme());
                            },
                            'property' => 'description',
                        ]
                    );

                    $areas = [];
                    if ($site instanceof AreaProviderInterface) {
                        $areas['Зоны, обрабатываемые сайтом'] = array_combine($site->getAreas(), $site->getAreas());
                    }

                    if ($this->themeRegistry->has($site->getTheme())) {
                        $theme = $this->themeRegistry->get($site->getTheme());
                        if ($theme instanceof AreaProviderInterface) {
                            $areas['Зоны, темы '.$theme->getType()] = array_combine(
                                $theme->getAreas(),
                                $theme->getAreas()
                            );
                        }
                    }

                    foreach ($site->getBlocks() as $block) {
                        if ($block instanceof AreaProviderInterface) {
                            $areas['Зоны блока '.$block->getType()] = array_combine(
                                $block->getAreas(),
                                $block->getAreas()
                            );
                        }
                    }

                    $form->add(
                        'blocks',
                        'collection',
                        [
                            'type' => new SiteBlockType(),
                            'options' => ['areas' => $areas],
                            'allow_add' => true,
                            'allow_delete' => true,
                        ]
                    );
                }
            }
        );
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => SeasonedSite::class,
                'attr' => ['style' => 'horizontal'],
            ]
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'site';
    }
}
