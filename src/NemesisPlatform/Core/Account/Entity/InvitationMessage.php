<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 14.08.2014
 * Time: 16:36
 */

namespace NemesisPlatform\Core\Account\Entity;

class InvitationMessage extends AbstractMessage
{
    private $team;

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }
}
