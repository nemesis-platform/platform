<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-08-12
 * Time: 22:29
 */

namespace NemesisPlatform\Core\Account\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface NamedUserInterface extends AdvancedUserInterface
{
    /**
     * Returns formatted name
     *
     * Returns name formatted using followed terms:
     *  - %sl - first letter of last name
     *  - %sf - first letter of first name
     *  - %sm - first letter of middle name
     *  - %l - full last name
     *  - %f - full first name
     *  - %m - full middle name
     *
     * @param $format string
     *
     * @return string
     */
    public function getFormattedName($format = null);

    public function getId();
}
