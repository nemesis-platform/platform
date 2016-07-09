<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.12.2014
 * Time: 12:58
 */

namespace NemesisPlatform\Core\CMS\Entity;

use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;

class ProxyPage
{
    /** @var  int|null */
    private $id;
    /** @var  string */
    private $alias;
    /** @var  string */
    private $route;
    /** @var  array */
    private $data;
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
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
