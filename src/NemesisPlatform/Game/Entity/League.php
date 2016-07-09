<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-09-28
 * Time: 12:37
 */

namespace NemesisPlatform\Game\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class League
{
    /** @var  int|null */
    private $id;
    /** @var  string */
    private $name;
    /** @var  ArrayCollection|UserCategory[] */
    private $categories;
    /** @var  Season */
    private $season;
    /** @var bool */
    private $with_combined = false;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->name       = 'N\A';
    }

    /**
     * @return boolean
     */
    public function isWithCombined()
    {
        return $this->with_combined;
    }

    /**
     * @param boolean $with_combined
     */
    public function setWithCombined($with_combined)
    {
        $this->with_combined = $with_combined;
    }

    public function addCategory(UserCategory $category)
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setLeague($this);
        }
    }

    public function removeCategory(UserCategory $category)
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->setLeague(null);
        }
    }

    /**
     * @return Season
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param Season $season
     */
    public function setSeason($season)
    {
        $this->season = $season;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection|UserCategory[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param ArrayCollection|UserCategory[] $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
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
}
