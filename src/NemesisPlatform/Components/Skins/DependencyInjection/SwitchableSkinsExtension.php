<?php

namespace NemesisPlatform\Components\Skins\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SwitchableSkinsExtension extends Extension implements PrependExtensionInterface
{
    /** {@inheritdoc} */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }


    /** {@inheritdoc} */
    public function prepend(ContainerBuilder $container)
    {
        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'twig':
                    $container->prependExtensionConfig(
                        $name,
                        [
                            'globals' => [
                                'skin_registry' => '@nemesis.skin_registry',
                                'theme'         => '@nemesis.theme_provider',
                            ],
                        ]
                    );
                    break;
            }
        }
    }


}
