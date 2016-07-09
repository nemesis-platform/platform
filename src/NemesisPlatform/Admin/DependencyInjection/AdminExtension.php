<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 17.12.2014
 * Time: 11:50
 */

namespace NemesisPlatform\Admin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AdminExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('generators.yml');
        $loader->load('exporters.yml');
        $loader->load('importers.yml');
        $loader->load('widgets.yml');
        $loader->load('forms.yml');
        $loader->load('menu.yml');
        $loader->load('theme.yml');
        $loader->load('form_extensions.yml');
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
                            'form_themes' => [
                                'AdminBundle:Form:jasny_fileinput.html.twig',
                                'AdminBundle:Form:news.html.twig',
                                'AdminBundle:Form:menu.html.twig',
                            ],
                        ]
                    );
                    break;
            }
        }
    }
}
