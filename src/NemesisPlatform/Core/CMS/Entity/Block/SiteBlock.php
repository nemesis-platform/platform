<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 03.07.2015
 * Time: 13:18
 */

namespace NemesisPlatform\Core\CMS\Entity\Block;

use NemesisPlatform\Components\MultiSite\Entity\MultiSiteElement;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;

class SiteBlock implements MultiSiteElement
{
    /** @var  int|null */
    private $id;
    /** @var  AbstractBlock */
    private $block;
    /** @var  string */
    private $area;
    /** @var  SiteInterface */
    private $site;
    /** @var  int */
    private $weight = 0;

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AbstractBlock
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param AbstractBlock $block
     */
    public function setBlock(AbstractBlock $block)
    {
        $this->block = $block;
    }

    /**
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param string $area
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

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
    public function setSite(SiteInterface $site = null)
    {
        $this->site = $site;
    }

    /**
     * @param SiteInterface $site
     *
     * @return bool
     */
    public function belongsToSite(SiteInterface $site)
    {
        return $this->site === $site;
    }
}
