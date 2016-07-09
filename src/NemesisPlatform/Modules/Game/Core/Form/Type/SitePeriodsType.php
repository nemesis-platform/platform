<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 29.06.2015
 * Time: 17:23
 */

namespace NemesisPlatform\Modules\Game\Core\Form\Type;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Components\MultiSite\Service\SiteManagerInterface;
use NemesisPlatform\Modules\Game\Core\Entity\Period;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SitePeriodsType extends AbstractType
{
    /** @var  SiteManagerInterface */
    private $siteManager;

    /**
     * SitePeriodsType constructor.
     *
     * @param SiteManagerInterface $siteManager
     */
    public function __construct(SiteManagerInterface $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'site_periods';
    }

    public function getParent()
    {
        return 'entity';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'label'         => 'Период',
                'class'         => Period::class,
                'multiple'      => false,
                'required'      => true,
                'attr'          => [
                    'help_text' => 'Выберите период',
                ],
                'group_by'      => 'round.name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                              ->addOrderBy('p.round', 'DESC')
                              ->leftJoin('p.round', 'round')
                              ->leftJoin('round.season', 'season')
                              ->andWhere('season IN (:seasons)')
                              ->setParameter('seasons', $this->siteManager->getSite()->getSeasons()->toArray())
                              ->addOrderBy('p.position', 'ASC');
                },
            ]
        );
    }
}
