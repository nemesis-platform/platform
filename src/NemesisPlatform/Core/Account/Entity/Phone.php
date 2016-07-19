<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 11.02.14
 * Time: 11:13
 */

namespace NemesisPlatform\Core\Account\Entity;

use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class Phone
 *
 * @package Entity
 */
class Phone
{

    const STATUS_UNCONFIRMED = 0;
    const STATUS_PENDING     = 1;
    const STATUS_ACTIVE      = 2;
    const STATUS_ARCHIVED    = 3;
    private static $prefix = '+7';
    /**
     * @var int|null
     */
    private $id = null;
    /**
     * @var int
     */
    private $phonenumber = null;
    /**
     * @var UserInterface
     */
    private $user;
    /**
     * @var DateTime
     */
    private $first_confirmed = null;
    /**
     * @var int
     */
    private $status = self::STATUS_UNCONFIRMED;
    /**
     * @var string
     */
    private $code = null;

    /**
     * @return string
     */
    public static function getPrefix()
    {
        return self::$prefix;
    }

    /**
     * @param string $prefix
     */
    public static function setPrefix($prefix)
    {
        self::$prefix = $prefix;
    }

    public static function generateCode()
    {
        $digits = 6;
        $min    = pow(10, $digits - 1);
        $max    = pow(10, $digits) - 1;

        return str_pad(mt_rand($min, $max), $digits, '0', STR_PAD_LEFT);
    }

    public function getFullPhoneNumber()
    {
        return self::$prefix.$this->phonenumber;
    }

    /**
     * @return DateTime
     */
    public function getFirstConfirmed()
    {
        return $this->first_confirmed;
    }

    /**
     * @param DateTime $first_confirmed
     */
    public function setFirstConfirmed($first_confirmed)
    {
        $this->first_confirmed = $first_confirmed;
    }

    /**
     * @return int
     */
    public function getPhonenumber()
    {
        return $this->phonenumber;
    }

    /**
     * @param int $phonenumber
     */
    public function setPhonenumber($phonenumber)
    {
        $this->phonenumber = $phonenumber;
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
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->status == self::STATUS_ACTIVE || $this->status == self::STATUS_ARCHIVED;
    }

    /**
     * @return bool
     */
    public function isPendingConfirmation()
    {
        return $this->status == self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isNotConfirmed()
    {
        return $this->status == self::STATUS_UNCONFIRMED;
    }

    /**
     * @return int|null
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return (string)$this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }


    public function checkCode($code)
    {
        if ($this->code == $code) {
            return true;
        }
        $this->status = Phone::STATUS_UNCONFIRMED;
        $this->code   = null;

        return false;
    }

    /**
     * @return \NemesisPlatform\Core\Account\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \NemesisPlatform\Core\Account\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function __toString()
    {
        return "{$this->phonenumber}";
    }
}
