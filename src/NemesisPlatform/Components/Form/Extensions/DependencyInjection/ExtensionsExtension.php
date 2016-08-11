<?php

namespace NemesisPlatform\Components\Form\Extensions\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ExtensionsExtension extends Extension implements PrependExtensionInterface
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
                            'form' => [
                                'resources' => [
                                    'ExtensionsBundle:Form:datetime_local.html.twig',
                                    'ExtensionsBundle:Form:entity_autocomplete.html.twig',
                                ],
                            ],
                        ]
                    );
                    break;
            }
        }
    }
}
