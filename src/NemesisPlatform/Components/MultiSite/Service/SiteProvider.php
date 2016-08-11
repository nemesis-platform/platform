<?php

namespace NemesisPlatform\Components\MultiSite\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteProvider implements SiteProviderInterface
{
    /** @var null|SiteInterface */
    private $site;
    /** @var  string[] */
    private $maintenanceUrl = [];
    /** @var  SiteFactoryInterface */
    private $fallbackFactory;
    /** @var ObjectRepository */
    private $repository;

    /**
     * @param string[]             $maintenanceUrl
     * @param ObjectRepository     $repository
     * @param SiteFactoryInterface $fallbackFactory
     */
    public function __construct(
        array $maintenanceUrl,
        ObjectRepository $repository,
        SiteFactoryInterface $fallbackFactory
    ) {
        $this->maintenanceUrl  = $maintenanceUrl;
        $this->repository      = $repository;
        $this->fallbackFactory = $fallbackFactory;
    }

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        if (null === $this->site) {
            $this->site = $this->fallbackFactory->createSite(
                [
                    'name'        => '',
                    'description' => 'Ошибка',
                    'short_name'  => 'Ошибка',
                ]
            );
        }

        return $this->site;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws NotFoundHttpException
     */
    public function onRequest(GetResponseEvent $event)
    {
        if ($this->site) {
            return;
        }

        if (!$event->isMasterRequest()) {
            return;
        }

        $this->site = $this->detectSite($event->getRequest());
    }

    /**
     * @param Request $request
     *
     * @return SiteInterface
     *
     * @throws NotFoundHttpException
     */
    private function detectSite(Request $request)
    {
        /** @var SiteInterface[] $sites */
        $sites = $this->repository->findAll();
        foreach ($sites as $site) {
            if ($site->match($request)) {
                return $site;
            }
        }

        if ($this->isMaintenanceRequest($request)) {
            return $this->fallbackFactory->createSite();
        }

        throw new NotFoundHttpException('Данный сайт не зарегистрирован');
    }

    private function isMaintenanceRequest(Request $request)
    {
        return in_array($request->getHost(), $this->maintenanceUrl, true);
    }
}
