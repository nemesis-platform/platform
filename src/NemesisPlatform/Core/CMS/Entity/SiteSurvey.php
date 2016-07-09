<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 28.05.2015
 * Time: 15:44
 */

namespace NemesisPlatform\Core\CMS\Entity;

use NemesisPlatform\Components\Form\Survey\Entity\Survey;
use NemesisPlatform\Components\MultiSite\Entity\MultiSiteElement;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;

class SiteSurvey extends Survey implements MultiSiteElement
{
    /** @var  SiteInterface */
    private $site;

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param SiteInterface $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /** {@inheritdoc} */
    public function belongsToSite(SiteInterface $site)
    {
        return $this->site === $site;
    }
}
