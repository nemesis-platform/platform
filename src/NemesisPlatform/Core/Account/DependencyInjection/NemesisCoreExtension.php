<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-07-07
 * Time: 22:12
 */

namespace NemesisPlatform\Core\Account\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class NemesisCoreExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('security.yml');
        $loader->load('services.yml');
        $loader->load('forms.yml');
        $loader->load('menu.yml');
    }
}
