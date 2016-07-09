<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 03.07.2015
 * Time: 13:08
 */

namespace NemesisPlatform\Game\Entity\Block;

use NemesisPlatform\Core\CMS\Entity\Block\AbstractBlock;

class SimpleAccountTeamWidget extends AbstractBlock
{
    /**
     * @param array $options
     *
     * @return string
     */
    public function getTemplate(array $options = [])
    {
        return 'GameBundle:Block:simple_account_team.html.twig';
    }

    /**
     * @return string Name key for the object
     */
    public function getType()
    {
        return 'simple_account_team';
    }
}
