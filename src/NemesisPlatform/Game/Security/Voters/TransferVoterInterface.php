<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.12.2014
 * Time: 12:03
 */

namespace NemesisPlatform\Game\Security\Voters;

interface TransferVoterInterface
{
    const IS_REQUESTING = 'user_is_requesting';
    const IS_INVITED    = 'user_is_invited';
    const IS_MEMBER     = 'user_is_member';
}
