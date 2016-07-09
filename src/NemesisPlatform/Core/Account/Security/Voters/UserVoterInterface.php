<?php
/**
 * Created by PhpStorm.
 * User: scaytrase
 * Date: 19.12.2014
 * Time: 19:55
 */

namespace NemesisPlatform\Core\Account\Security\Voters;

interface UserVoterInterface
{
    const USER_UPDATE_ESSENTIALS = 'update_essentials';
    const USER_UPDATE_MISC       = 'update_misc';
    const USER_CONFIRM_PHONE     = 'confirm_phone';
    const USER_SWITCH_PHONE      = 'switch_phone';
    const USER_REGISTER          = 'register';
    const USER_APPLY_SEASON      = 'apply_season';
    const USER_CHANGE_PASSWORD   = 'change_password';
    const USER_RESTORE_PASSWORD  = 'restore_password';
}
