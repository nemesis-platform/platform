<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 04.02.14
 * Time: 17:44
 */

namespace NemesisPlatform\Core\Account\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Season;
use Serializable;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 *
 * @package Entity
 */
class User implements NamedUserInterface, EquatableInterface, EncoderAwareInterface, Serializable
{
    const EMAIL_PENDING   = 0;
    const EMAIL_CONFIRMED = 1;
    const BANNED          = 2;

    /**@var int|null */
    private $id;
    /**@var string */
    private $email;
    /** @var string */
    private $password = '';
    /** @var string */
    private $encoder = 'bcrypt';

    /** @var  int */
    private $status = self::EMAIL_PENDING;
    /** @var DateTime */
    private $registerDate;
    /** @var string */
    private $code = null;
    /** @var string */
    private $pwdCode = null;
    /** @var Phone[]|ArrayCollection */
    private $phones;
    /** @var RoleInterface[] */
    private $roles = [];
    /** @var  Phone */
    private $phone;
    /** @var  string */
    private $lastname;
    /** @var  string */
    private $firstname;
    /** @var  string */
    private $middlename;
    /** @var  string */
    private $avatar;
    /** @var  DateTime */
    private $birth_date;
    /** @var  Participant[]|ArrayCollection */
    private $participations;

    /** @var  string */
    private $about;
    /** @var  string */
    private $social_vkontakte;
    /** @var  string */
    private $social_twitter;
    /** @var  string */
    private $social_facebook;
    /** @var  ArrayCollection|Tag[] */
    private $tags;
    /** @var  string */
    private $admin_comment;

    /**
     * User constructor.
     *
     * @param string $email
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($email, $password, $firstName, $lastName)
    {
        $this->email          = $email;
        $this->password       = $password;
        $this->firstname      = $firstName;
        $this->lastname       = $lastName;
        $this->participations = new ArrayCollection();
        $this->phones         = new ArrayCollection();
        $this->registerDate   = new DateTime();
        $this->tags           = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getAdminComment()
    {
        return $this->admin_comment;
    }

    /**
     * @param mixed $admin_comment
     */
    public function setAdminComment($admin_comment)
    {
        $this->admin_comment = $admin_comment;
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

    /**
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @param string $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * @return string
     */
    public function getSocialFacebook()
    {
        return $this->social_facebook;
    }

    /**
     * @param string $social_facebook
     */
    public function setSocialFacebook($social_facebook)
    {
        $this->social_facebook = $social_facebook;
    }

    /**
     * @return string
     */
    public function getSocialTwitter()
    {
        return $this->social_twitter;
    }

    /**
     * @param string $social_twitter
     */
    public function setSocialTwitter($social_twitter)
    {
        $this->social_twitter = $social_twitter;
    }

    /**
     * @return string
     */
    public function getSocialVkontakte()
    {
        return $this->social_vkontakte;
    }

    /**
     * @param string $social_vkontakte
     */
    public function setSocialVkontakte($social_vkontakte)
    {
        $this->social_vkontakte = $social_vkontakte;
    }

    /**
     * @return Participant[]|ArrayCollection
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * @param ArrayCollection|Participant[] $participations
     */
    public function setParticipations($participations)
    {
        $this->participations = $participations;
    }

    /**
     * @param Season $season
     *
     * @return Participant|null
     */
    public function getParticipation(\NemesisPlatform\Game\Entity\Season $season)
    {
        foreach ($this->participations as $data) {
            if ($data->getSeason() === $season) {
                return $data;
            }
        }

        return null;
    }

    public function isFrozen()
    {
        foreach ($this->participations as $data) {
            if ($data->isFrozen()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return mixed
     */
    public function getBirthdate()
    {
        return $this->birth_date;
    }

    /**
     * @param mixed $birthdate
     */
    public function setBirthdate($birthdate)
    {
        $this->birth_date = $birthdate;
    }

    /**
     * @return string
     */
    public function getPwdCode()
    {
        return $this->pwdCode;
    }

    /**
     * @param string $pwdCode
     */
    public function setPwdCode($pwdCode)
    {
        $this->pwdCode = $pwdCode;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return ArrayCollection|Phone[]
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @return DateTime
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * @param DateTime $registerDate
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return Phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param Phone $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return boolean
     */
    public function isEmailPublic()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->avatar;
    }

    /**
     * @param string $photo
     */
    public function setPhoto($photo)
    {
        $this->avatar = $photo;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function hasPhoto()
    {
        return $this->avatar !== null && $this->avatar != '0' && $this->avatar != '';
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->id;
    }


    /**
     * @return bool
     */
    public function hasUnconfirmedNumber()
    {
        $result = false;

        foreach ($this->phones as $phone) {
            $result = $result || $phone->getStatus() === Phone::STATUS_UNCONFIRMED;
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function hasConfirmedNumbers()
    {
        foreach ($this->phones as $phone) {
            if ($phone->isConfirmed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasPendingNumber()
    {
        foreach ($this->phones as $phone) {
            if ($phone->isPendingConfirmation()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Phone|null
     */
    public function getPendingNumber()
    {
        foreach ($this->phones as $phone) {
            if ($phone->isPendingConfirmation()) {
                return $phone;
            }
        }

        return null;
    }

    /**
     * @return Phone|null
     */
    public function getUnconfirmedNumber()
    {
        foreach ($this->phones as $phone) {
            if ($phone->isNotConfirmed()) {
                return $phone;
            }
        }

        return null;
    }

    /** {@inheritdoc} */
    public function isAccountNonExpired()
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isAccountNonLocked()
    {
        return $this->status !== self::BANNED;
    }

    /** {@inheritdoc} */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isEnabled()
    {
        return $this->status === self::EMAIL_CONFIRMED;
    }

    /** {@inheritdoc} */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __toString()
    {
        return '['.$this->id.'] '.$this->getFormattedName('%l %f %m');
    }

    /** {@inheritdoc} */
    public function getFormattedName($format = '%l %f %m')
    {
        $result = $format;
        $result = str_replace('%l', $this->getLastname(), $result);
        $result = str_replace('%f', $this->getFirstname(), $result);
        $result = str_replace('%m', $this->getMiddlename(), $result);

        $result = str_replace('%sl', mb_substr($this->getLastname(), 0, 1, 'utf-8'), $result);
        $result = str_replace('%sf', mb_substr($this->getFirstname(), 0, 1, 'utf-8'), $result);
        $result = str_replace('%sm', mb_substr($this->getMiddlename(), 0, 1, 'utf-8'), $result);

        return $result;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * @param string $middlename
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;
    }

    public function addPhone(Phone $phone)
    {
        $phone->setUser($this);
        if (!$this->phones->contains($phone)) {
            $this->phones->add($phone);
        }
    }

    public function removePhone(Phone $phone)
    {
        if ($this->phones->contains($phone)) {
            $phone->setUser(null);
            $this->phones->removeElement($phone);
        }
    }

    /** {@inheritdoc} */
    public function isEqualTo(UserInterface $user)
    {
        if ($user->getUsername() !== $this->getUsername()) {
            return false;
        }
        if ($user->getPassword() !== $this->getPassword()) {
            return false;
        }
        if ($user->getSalt() !== $this->getSalt()) {
            return false;
        }

        if (count($user->getRoles()) !== count($this->getRoles())) {
            return false;
        }

        foreach ($this->getRoles() as $role) {
            if (!in_array($role, $user->getRoles(), true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $encodedPassword
     */
    public function setPassword($encodedPassword)
    {
        $this->password = $encodedPassword;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        if ('bcrypt' === $this->encoder) {
            return null;
        }

        return $this->email;
    }

    /** {@inheritdoc} */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param RoleInterface[] $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /** {@inheritdoc} */
    public function getEncoderName()
    {
        return $this->encoder;
    }

    /** {@inheritdoc} */
    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->email,
                $this->password,
                $this->getSalt(),
            ]
        );
    }

    /** {@inheritdoc} */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt
            )
            = unserialize($serialized);
    }

    public function confirm()
    {
        $this->code   = null;
        $this->status = self::EMAIL_CONFIRMED;
    }
}
