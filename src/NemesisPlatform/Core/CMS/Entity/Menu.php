<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-26
 * Time: 23:47
 */

namespace NemesisPlatform\Core\CMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Components\MultiSite\Entity\MultiSiteElement;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;

class Menu implements MultiSiteElement
{
    /** @var  int|null */
    private $id;
    /** @var  string */
    private $name;
    /** @var  MenuElement[]|ArrayCollection */
    private $elements;
    /** @var  SiteInterface */
    private $site;

    /**
     * Menu constructor.
     *
     * @param string        $name
     * @param SiteInterface $site
     */
    public function __construct($name, SiteInterface $site)
    {
        $this->name     = $name;
        $this->site     = $site;
        $this->elements = new ArrayCollection();
    }


    public function addElement(MenuElement $element)
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
        }
    }

    public function removeElement(MenuElement $element)
    {
        if ($this->elements->contains($element)) {
            $this->elements->removeElement($element);
        }
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
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @return MenuElement[]|ArrayCollection
     */
    public function getElements()
    {
        return $this->elements->toArray();
    }

    public function getRootElements()
    {
        return $this->elements->filter(
            function (MenuElement $element) {
                return $element->getParent() === null;
            }
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param SiteInterface|SiteInterface $site
     *
     * @return bool
     */
    public function belongsToSite(SiteInterface $site)
    {
        return $this->site === $site;
    }

    public function sanitize()
    {
        foreach ($this->elements as $element) {
            foreach ($element->getChildren() as $child) {
                if (!$this->elements->contains($child)) {
                    $element->removeChild($child);
                }
            }
        }
    }
}
