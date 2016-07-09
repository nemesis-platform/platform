<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.05.2015
 * Time: 12:52
 */

namespace NemesisPlatform\Game\Entity\Certificate;

use NemesisPlatform\Game\Entity\Team;

class TeamCertificate extends AbstractCertificate
{
    /** @var Team */
    private $team;

    /**
     * @param string          $link
     * @param CertificateType $type
     * @param Team            $team
     */
    public function __construct($link, CertificateType $type, Team $team)
    {
        parent::__construct($link, $type);
        $this->team = $team;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Team $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }
}
