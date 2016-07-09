<?php
/**
 * Created by PhpStorm.
 * User: scaytrase
 * Date: 19.12.2014
 * Time: 19:51
 */

namespace NemesisPlatform\Game\Security\Voters;

interface ParticipantVoterInterface
{
    const PARTICIPANT_TEAM_CREATE        = 'create_team';
    const PARTICIPANT_UPDATE_SEASON_DATA = 'update_season_data';
}
