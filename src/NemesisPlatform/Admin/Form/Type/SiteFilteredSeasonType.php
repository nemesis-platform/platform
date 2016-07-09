<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-05-26
 * Time: 21:28
 */

namespace NemesisPlatform\Admin\Form\Type;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Components\MultiSite\Service\SiteManagerInterface;
use NemesisPlatform\Game\Entity\Season;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteFilteredSeasonType extends AbstractType
{
    /** @var  SiteManagerInterface */
    private $siteManager;

    /**
     * SiteFilteredSeasonType constructor.
     *
     * @param SiteManagerInterface $siteManager
     */
    public function __construct(SiteManagerInterface $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'label'         => 'Сезон',
                'class'         => Season::class,
                'multiple'      => false,
                'required'      => true,
                'group_by'      => 'site',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                              ->andWhere('s.site =:site')
                              ->setParameter('site', $this->siteManager->getSite())
                              ->addOrderBy('s.short_name', 'DESC');
                },
            ]
        );
    }

    public function getParent()
    {
        return 'entity';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'site_seasons';
    }
}
