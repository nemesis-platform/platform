<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.05.2015
 * Time: 12:23
 */

namespace NemesisPlatform\Game\Entity\Certificate;

use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Season;

class ParticipantCertificate extends AbstractCertificate
{
    /** @var  Season */
    private $season;
    /** @var  \NemesisPlatform\Core\Account\Entity\User */
    private $owner;

    /**
     * @param string                                         $link
     * @param CertificateType                                $type
     * @param \NemesisPlatform\Core\Account\Entity\User $owner
     * @param \NemesisPlatform\Game\Entity\Season       $season
     */
    public function __construct($link, CertificateType $type, user $owner, Season $season)
    {
        parent::__construct($link, $type);
        $this->owner  = $owner;
        $this->season = $season;
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
     * @return \NemesisPlatform\Core\Account\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param \NemesisPlatform\Core\Account\Entity\User $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }
}
