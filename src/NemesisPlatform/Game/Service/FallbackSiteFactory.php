<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 13.04.2015
 * Time: 12:58
 */

namespace NemesisPlatform\Game\Service;

use NemesisPlatform\Components\MultiSite\Service\SiteFactoryInterface;
use NemesisPlatform\Game\Entity\SeasonedSite;

class FallbackSiteFactory implements SiteFactoryInterface
{
    public function createSite(array $options = [])
    {
        $options = $this->getOptions($options);

        $site = new SeasonedSite($options['maintenance_url'][0], $options['short_name']);
        $site->setTheme('basic_bootstrap_theme');

        return $site;
    }
    
    private function getOptions(array $options = [])
    {
        $defaults = [
            'active'          => false,
            'name'            => 'Режим обслуживания',
            'short_name'      => 'Обслуживание',
            'description'     => 'Режим обслуживания',
            'maintenance_url' => 'localhost',
        ];

        return array_replace_recursive($defaults, $options);
    }
}
