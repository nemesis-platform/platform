<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-07-07
 * Time: 22:12
 */

namespace NemesisPlatform\Core\CMS\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CMSExtension extends Extension implements PrependExtensionInterface
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
        $loader->load('blocks.yml');
        $loader->load('services.yml');
        $loader->load('menu.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'twig':
                    $container->prependExtensionConfig(
                        $name,
                        [
                            'globals'     => [
                                'menu_registry' => '@nemesis.registry.menu',
                            ],
                            'form_themes' => [
                                'CoreBundle:Form:rules_checkbox.html.twig',
//                                '@bootstrap_bridge/table.html.twig',
//                                '@bootstrap_bridge/table_row.html.twig',
                            ],
                            'paths'       => [
//                                '%kernel.root_dir%/../src/ScayTrase/BraincraftedBootstrapBridge/Resources/views/Form' => 'bootstrap_bridge',
                            ],
                        ]
                    );
                    break;
            }
        }
    }
}
