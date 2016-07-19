<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-04-29
 * Time: 21:33
 */

namespace NemesisPlatform\Game\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;
use NemesisPlatform\Components\MultiSite\Entity\MultiSiteElement;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Core\Account\Entity\User;

class Participant implements MultiSiteElement
{
    /** @var  int|null */
    private $id;
    /** @var  Season */
    private $season;
    /** @var  DateTime */
    private $created;
    /** @var  User */
    private $user;
    /** @var  UserCategory */
    private $category;
    /** @var  Team[]|ArrayCollection */
    private $teams;
    /** @var  Team[]|ArrayCollection */
    private $teamRequests;
    /** @var  Team[]|ArrayCollection */
    private $teamInvites;

    /** @var AbstractValue[]|ArrayCollection */
    private $values;

    public function __construct()
    {
        $this->created      = new DateTime();
        $this->teams        = new ArrayCollection();
        $this->teamInvites  = new ArrayCollection();
        $this->teamRequests = new ArrayCollection();
        $this->values       = new ArrayCollection();
    }

    public function addValue(AbstractValue $value)
    {
        while ($oldValue = $this->findSameValue($value)) {
            $this->removeValue($oldValue);
        }

        if ($this->getSeasonFields()->contains($value->getField())) {
            $this->values->add($value);
        }
    }

    /**
     * @param AbstractValue $value
     *
     * @return AbstractValue
     */
    private function findSameValue(AbstractValue $value)
    {
        /** @var AbstractValue $sameValue */
        $sameValue = null;

        foreach ($this->values as $storedValue) {
            if ($storedValue->getField() === $value->getField()) {
                $sameValue = $storedValue;
            }
        }

        return $sameValue;
    }

    public function removeValue(AbstractValue $value)
    {
        if ($this->getSeasonFields()->contains($value->getField())) {
            $this->values->removeElement($value);
        }
    }

    /**
     * @return ArrayCollection|AbstractField[]
     */
    private function getSeasonFields()
    {
        return $this->getCategory()->getFields();
    }

    /**
     * @return UserCategory
     */
    public function getCategory()
    {
        return $this->category ?: new UserCategory();
    }

    /**
     * @param UserCategory $category
     */
    public function setCategory(UserCategory $category)
    {
        if ($category !== $this->category) {
            foreach ($this->teams as $team) {
                $team->setLeague($category ? $category->getLeague() : null);
            }
        }

        $this->category = $category;
    }

    public function sanitizeValues()
    {
        foreach ($this->values as $name => $value) {
            if (!$value || !$value->getValue()) {
                $this->values->remove($name);
            }
        }
    }

    /**
     * @return AbstractField[]
     */
    public function getStoredFields()
    {
        return array_map(
            function (AbstractValue $value) {
                return $value->getField();
            },
            $this->values
        );
    }

    /**
     * @return ArrayCollection|AbstractValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param ArrayCollection|AbstractValue[] $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }


    public function getLeague()
    {
        return $this->category ? $this->category->getLeague() : ($this->season->getCombinedLeague() ?: new League());
    }

    /**
     * @return bool
     */
    public function isFrozen()
    {
        foreach ($this->teams as $team) {
            if ($team->isFrozen() || !$team->getSeason()->isActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Team[]|ArrayCollection
     */
    public function getTeamInvites()
    {
        return $this->teamInvites;
    }

    /**
     * @param Team[] $invites
     */
    public function setTeamInvites($invites)
    {
        $this->teamInvites = $invites;
    }

    /**
     * @return Team[]|ArrayCollection
     */
    public function getTeamRequests()
    {
        return $this->teamRequests;
    }

    /**
     * @param Team[] $requests
     */
    public function setTeamRequests($requests)
    {
        $this->teamRequests = $requests;
    }

    /**
     * @return Team[]|ArrayCollection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param Team[] $teams
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function toString()
    {
        return (string)$this;
    }

    public function __toString()
    {
        return $this->user->getFormattedName().' ('.$this->season->getSite()->getFullName().' '
               .$this->season->getName().')';
    }

    /** {@inheritdoc} */
    public function belongsToSite(SiteInterface $site)
    {
        return $this->getSeason()->getSite() === $site;
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
     * Cleans all teams of participant
     */
    public function cleanTeams()
    {
        foreach ($this->getTeams() as $team) {
            $team->getMembers()->removeElement($this);
            $this->getTeams()->removeElement($team);
        }

        foreach ($this->getTeamInvites() as $team) {
            $team->getInvites()->removeElement($this);
            $this->getTeamInvites()->removeElement($team);
        }

        foreach ($this->getTeamRequests() as $team) {
            $team->getRequests()->removeElement($this);
            $this->getTeamRequests()->removeElement($team);
        }
    }
}
