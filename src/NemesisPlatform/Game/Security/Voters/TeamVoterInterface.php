<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 12.12.2014
 * Time: 18:10
 */

namespace NemesisPlatform\Game\Security\Voters;

interface TeamVoterInterface
{
    const TEAM_CREATE          = 'create_team';
    const TEAM_JOIN            = 'join';
    const TEAM_LEAVE           = 'leave';
    const TEAM_DISBAND         = 'disband';
    const TEAM_MANAGE          = 'manage';
    const TEAM_KICK            = 'kick';
    const TEAM_INVITE          = 'invite';
    const TEAM_REVOKE_INVITE   = 'revoke_invite';
    const TEAM_ACCEPT_INVITE   = 'accept_invite';
    const TEAM_DECLINE_INVITE  = 'decline_invite';
    const TEAM_REQUEST         = 'request';
    const TEAM_REVOKE_REQUEST  = 'revoke_request';
    const TEAM_ACCEPT_REQUEST  = 'accept_request';
    const TEAM_DECLINE_REQUEST = 'decline_request';
}
