<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.08.2014
 * Time: 18:08
 */

namespace NemesisPlatform\Game\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Components\MultiSite\Entity\MultiSiteElement;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Game\Entity\Rule;
use NemesisPlatform\Game\Entity\Rule\RuleCheckerTrait;
use NemesisPlatform\Game\Entity\Rule\RuleContainerCheckerInterface;
use NemesisPlatform\Game\Entity\Rule\RuleContainerInterface;
use NemesisPlatform\Game\Entity\Rule\RuleInterface;

class Season implements RuleContainerInterface, RuleContainerCheckerInterface, MultiSiteElement
{
    use RuleCheckerTrait;

    /** @var  int|null */
    private $id;
    /** @var  string */
    private $name;
    /** @var  string */
    private $short_name;
    /** @var  string */
    private $description;
    /** @var  SiteInterface */
    private $site;
    /** @var bool */
    private $active = true;
    /** @var bool */
    private $registration_open = false;
    /** @var ArrayCollection|League[] */
    private $leagues;

    /** @var Rule\RuleInterface[] */
    private $rules;
    /** @var Team[]|ArrayCollection */
    private $teams;

    public function __construct()
    {
        $this->leagues = new ArrayCollection();
        $this->rules   = new ArrayCollection();
        $this->teams   = new ArrayCollection();
    }

    public function getCombinedLeague()
    {
        foreach ($this->leagues as $league) {
            if ($league->isWithCombined()) {
                return $league;
            }
        }

        return null;
    }

    /**
     * @return UserCategory|null
     */
    public function getCombinedCategory()
    {
        foreach ($this->leagues as $league) {
            if ($league->isWithCombined()) {
                $category = new UserCategory();
                $category->setLeague($league);
                $category->setName('Сборная категория');

                return $category;
            }
        }

        return null;
    }

    /**
     * @return ArrayCollection|League[]
     */
    public function getLeagues()
    {
        return $this->leagues;
    }

    /**
     * @param ArrayCollection|League[] $leagues
     */
    public function setLeagues($leagues)
    {
        $this->leagues = $leagues;

        foreach ($leagues as $league) {
            $league->setSeason($this);
        }
    }

    public function addLeague(League $league)
    {
        if (!$this->leagues->contains($league)) {
            $this->leagues->add($league);
            $league->setSeason($this);
        }
    }

    public function removeLeague(League $league)
    {
        if ($this->leagues->contains($league)) {
            $this->leagues->removeElement($league);
            $league->setSeason(null);
        }
    }

    /**
     * @return boolean
     */
    public function isRegistrationOpen()
    {
        return $this->registration_open;
    }

    /**
     * @param boolean $registration_open
     */
    public function setRegistrationOpen($registration_open)
    {
        $this->registration_open = $this->isActive() && $registration_open;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return string
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * @param string $short_name
     */
    public function setShortName($short_name)
    {
        $this->short_name = $short_name;
    }

    public function __toString()
    {
        return $this->name.' - '.((string)$this->site);
    }

    /**
     * @return RuleInterface[]|ArrayCollection
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param RuleInterface[] $rules
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    public function checkRules($subject, $context)
    {
        foreach ($this->rules as $rule) {
            if ($rule->isEnabled() && $rule->isStrict() && $rule->isApplicable($subject)
                && !$rule->isValid(
                    $subject,
                    $context
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /** {@inheritdoc} */
    public function belongsToSite(SiteInterface $site)
    {
        return $this->site === $site;
    }
}
