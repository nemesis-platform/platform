<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 08.07.2015
 * Time: 15:57
 */

namespace NemesisPlatform\Game\ParamConverter;

use NemesisPlatform\Components\MultiSite\Service\SiteProvider;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\SeasonedSite;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SeasonParamConverter extends DoctrineParamConverter
{

    /** @var  SiteProvider */
    private $siteManager;

    /**
     * @param ManagerRegistry $registry
     * @param SiteProvider    $siteManager
     */
    public function __construct(ManagerRegistry $registry, SiteProvider $siteManager)
    {
        parent::__construct($registry);
        $this->siteManager = $siteManager;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request $request The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool    True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();


        $site = $this->siteManager->getSite();

        if (!$site) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        if (!($site instanceof SeasonedSite)) {
            return false;
        }

        if ($request->attributes->get($name, false) === null) {
            return false;
        }

        if ($name !== 'season') {
            $request->attributes->set(
                'season',
                $site->getSeasons()->map(
                    function (Season $season) {
                        return $season->getId();
                    }
                )->toArray()
            );
        }

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool    True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return in_array(
            'ScayTrase\MultiSiteBundle\Entity\SiteBoundInterface',
            class_implements($configuration->getClass()),
            true
        );
    }
}
