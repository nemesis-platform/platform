<?php

namespace NemesisPlatform\Components\Form\PersistentForms\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PersistentFormsExtension extends Extension implements PrependExtensionInterface, CompilerPassInterface
{
    /** {@inheritdoc} */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('field_types.yml');
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
                                'field_registry' => '@scaytrase.stored_forms.fields_registry',
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
        $registry = $container->getDefinition('scaytrase.stored_forms.fields_registry');

        $fields = $container->findTaggedServiceIds('storable_field');

        foreach ($fields as $id => $tags) {
            foreach ($tags as $tag) {
                $alias = array_key_exists('alias', $tag) ? $tag['alias'] : $id;
                $registry->addMethodCall('add', [$alias, new Reference($id)]);
            }
        }

    }
}
