<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.06.2015
 * Time: 15:42
 */

namespace NemesisPlatform\Modules\Game\Core\Form\Type;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Components\MultiSite\Service\SiteProviderInterface;
use NemesisPlatform\Modules\Game\Core\Entity\Round\Round;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoundChoiceType extends AbstractType
{
    /** @var  SiteProviderInterface */
    protected $siteManager;

    /**
     * RoundChoiceType constructor.
     *
     * @param SiteProviderInterface $siteManager
     */
    public function __construct(SiteProviderInterface $siteManager)
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
        return 'current_site_rounds';
    }

    public function getParent()
    {
        return 'entity';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'property'      => 'name',
                'data_class'    => Round::class,
                'query_builder' => function (EntityRepository $repository) {
                    /** @var \NemesisPlatform\Game\Entity\SeasonedSite $site */
                    $site = $this->siteManager->getSite();

                    return $repository->createQueryBuilder('r')
                                      ->andWhere('r.season in (:seasons)')
                                      ->setParameter('seasons', $site->getSeasons()->toArray());
                },
            ]
        );
    }
}
