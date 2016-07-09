<?php

namespace NemesisPlatform\Components\Themes\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SwitchableThemeExtension extends Extension implements PrependExtensionInterface, CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
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
                            'globals' => [
                                'theme_registry' => '@scaytrase.theme_registry',
                                'theme'          => '@site_theme',
                            ],
                        ]
                    );
                    break;
            }
        }
    }

    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('scaytrase.theme_registry')) {
            return;
        }

        $registry = $container->getDefinition('scaytrase.theme_registry');
        $themes   = $container->findTaggedServiceIds('theme');

        foreach ($themes as $id => $tags) {
            foreach ($tags as $tag) {
                $alias = array_key_exists('alias', $tag) ? $tag['alias'] : $id;
                $registry->addMethodCall('add', [$alias, new Reference($id)]);
            }
        }
    }
}
