<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.07.2015
 * Time: 15:07
 */

namespace NemesisPlatform\Core\CMS\Entity\Block;

class SideBarNewsWidget extends AbstractBlock
{

    /**
     * @param array $options
     *
     * @return string
     */
    public function getTemplate(array $options = [])
    {
        return 'CMSBundle:Block:side_news.html.twig';
    }

    /**
     * @return string Name key for the object
     */
    public function getType()
    {
        return 'sidebar_news';
    }
}
