<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 04.02.14
 * Time: 17:34
 */

namespace NemesisPlatform\Game\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Components\MultiSite\Entity\MultiSiteElement;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Core\Account\Entity\Tag;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Rule\Participant\SingleTeamRule;
use NemesisPlatform\Game\Entity\Rule\Team\MaxMembersCountRule;

class Team implements MultiSiteElement
{
    const MAX_MEMBERS = 5;

    /** @var int */
    private $id = null;
    /** @var string */
    private $name = '';
    /** @var Participant[]|ArrayCollection */
    private $members;
    /** @var Participant[]|ArrayCollection */
    private $invites;
    /** @var User[]|ArrayCollection */
    private $requests;

    /** @var Participant */
    private $captain;
    /** @var League */
    private $league;
    /** @var string */
    private $advert = '';
    /** @var null|boolean */
    private $frozen;
    /** @var DateTime */
    private $date;
    /** @var Season */
    private $season;
    /** @var null|string */
    private $persistent_tag;
    /** @var ArrayCollection|Tag[] */
    private $tags;
    /** @var  string */
    private $admin_comment;
    /** @var \DateTime|null */
    private $form_date;

    /**
     * Team constructor.
     *
     * @param string      $name
     * @param Participant $captain
     */
    public function __construct($name, Participant $captain)
    {
        $this->name           = $name;
        $this->captain        = $captain;
        $this->season         = $captain->getSeason();
        $this->persistent_tag = bin2hex(random_bytes(20));
        $this->date           = new DateTime();
        $this->members        = new ArrayCollection([$captain]);
        $this->requests       = new ArrayCollection();
        $this->invites        = new ArrayCollection();
        $this->tags           = new ArrayCollection();
    }

    /**
     * @return null|DateTime
     */
    public function getFormDate()
    {
        return $this->form_date;
    }

    /**
     * @param null|DateTime $form_date
     */
    public function setFormDate($form_date)
    {
        $this->form_date = $form_date;
    }

    /**
     * @return string
     */
    public function getAdminComment()
    {
        return $this->admin_comment;
    }

    /**
     * @param string $admin_comment
     */
    public function setAdminComment($admin_comment)
    {
        $this->admin_comment = $admin_comment;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return ArrayCollection|\NemesisPlatform\Core\Account\Entity\Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection|\NemesisPlatform\Core\Account\Entity\Tag[] $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function addMember(Participant $member)
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->getTeams()->add($this);
        }
    }

    public function removeMember(Participant $member)
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
            $member->getTeams()->removeElement($member);
        }
    }

    /** @return null|string */
    public function getPersistentTag()
    {
        return $this->persistent_tag;
    }

    /** @param null|string $persistent_tag */
    public function setPersistentTag($persistent_tag)
    {
        $this->persistent_tag = $persistent_tag;
    }

    /**
     * @return Participant[]|ArrayCollection
     */
    public function getInvites()
    {
        return $this->invites;
    }

    /**
     * @param array $invites
     */
    public function setInvites($invites)
    {
        $this->invites = $invites;
    }

    /**
     * @return int|null
     */
    public function getFrozen()
    {
        return $this->frozen === true;
    }

    /**
     * @param boolean|null $frozen
     */
    public function setFrozen($frozen)
    {
        $this->frozen = $frozen;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * @param string $advert
     */
    public function setAdvert($advert)
    {
        $this->advert = $advert;
    }


    /**
     * @return Participant[]|ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param $members Participant[]
     *
     * @return $this
     */
    public function setMembers($members)
    {
        $this->members = $members;

        return $this;
    }

    /**
     * @return Participant[]|ArrayCollection
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @param $members Participant[]
     *
     * @return $this
     */
    public function setRequests($members)
    {
        $this->requests = $members;

        return $this;
    }

    /**
     * @return League
     */
    public function getLeague()
    {
        return $this->league ?: $this->season->getCombinedLeague();
    }

    /**
     * @param League $league
     */
    public function setLeague($league)
    {
        $this->league = $league;
    }

    /**
     * @return mixed|string
     */
    public function getCleanName()
    {
        $cleanName = preg_replace('/[!#;$%^&*"\\/\'`]|$[ ]*|[ ]*$/', '', $this->getName());
        $cleanName = str_replace('\\', '', $cleanName);
        $cleanName = preg_replace('/^\d+[ ]*/', '', $cleanName);
        $cleanName = preg_replace('/^[ ]+/', '', $cleanName);
        if ($cleanName == '') {
            $cleanName = 'Z';
        }

        return $cleanName;
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
     * @param User $user
     *
     * @return bool|Participant|null
     */
    public function isAbleToRequest(User $user)
    {
        if ($this->hasMaxMembers()) {
            return false;
        }

        $participation = $user->getParticipation($this->getSeason());

        if (!$participation) {
            return false;
        }

        foreach ($this->season->getRules() as $rule) {
            if ($rule instanceof SingleTeamRule && $rule->isEnabled()) {
                foreach ($participation->getTeams() as $team) {
                    if (!$team->isAbleToLeave($user)) {
                        return false;
                    }
                }
            }
        }

        if (
            !$participation
            || $participation->isFrozen()
            || in_array($participation, $this->members->toArray())
            || in_array($participation, $this->requests->toArray())
            || in_array($participation, $this->invites->toArray())
        ) {
            return false;
        }

        return $participation;
    }

    public function hasMaxMembers()
    {
        $maxCount = null;
        foreach ($this->season->getRules() as $rule) {
            if ($rule instanceof MaxMembersCountRule) {
                $maxCount = $rule->getMax();
            }
        }

        return $maxCount ? count($this->members) >= $maxCount : false;
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

    public function isAbleToLeave(User $user)
    {
        if (!$this->isMember($user)) {
            return false;
        }

        if ($user === $this->getCaptain()->getUser()) {
            return false;
        }

        if ($this->isFrozen()) {
            return false;
        }

        return $this->isMember($user);
    }

    /**
     * @param User $user
     *
     * @return bool|Participant false if is not member, member data otherwise
     */
    public function isMember(User $user)
    {
        foreach ($this->members as $member) {
            if ($member->getUser() === $user) {
                return $member;
            }
        }

        return false;
    }

    /**
     * @return Participant
     */
    public function getCaptain()
    {
        return $this->captain;
    }

    /**
     * @param Participant $captain
     */
    public function setCaptain(Participant $captain)
    {
        $this->captain = $captain;
    }

    /**
     * Return true if team data cannot be modified by the captain
     *
     * @return bool
     */
    public function isFrozen()
    {
        return $this->frozen;
    }

    /**
     * @return bool
     */
    public function isAbleToAddUser()
    {
        return !$this->isFrozen() && !$this->hasMaxMembers();
    }

    /**
     * @param User $user
     *
     * @return bool|Participant|null
     */
    public function isAbleToJoin(User $user)
    {
        if ($this->hasMaxMembers()) {
            return false;
        }

        $participation = $user->getParticipation($this->getSeason());

        if (!$participation || $participation->isFrozen() || in_array($participation, $this->members->toArray())) {
            return false;
        }


        foreach ($this->season->getRules() as $rule) {
            if ($rule instanceof SingleTeamRule && $rule->isEnabled()) {
                foreach ($participation->getTeams() as $team) {
                    if (!$team->isAbleToLeave($user)) {
                        return false;
                    }
                }
            }
        }

        return $participation;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isAbleToManage(User $user)
    {
        return !$this->isFrozen() && $this->season->isActive() && $this->captain->getUser() === $user;
    }

    /**
     * @param User $user
     *
     * @return bool|Participant false if is not requesting, member data otherwise
     */
    public function isRequesting(User $user)
    {
        foreach ($this->requests as $member) {
            if ($member->getUser() === $user) {
                return $member;
            }
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool|Participant false if is not invited, member data otherwise
     */
    public function isInvited(User $user)
    {
        foreach ($this->invites as $member) {
            if ($member->getUser() === $user) {
                return $member;
            }
        }

        return false;
    }

    public function __toString()
    {
        return sprintf('%s [%s]', $this->name, $this->season);
    }

    /** {@inheritdoc} */
    public function belongsToSite(SiteInterface $site)
    {
        return $this->getSeason()->getSite() === $site;
    }
}
