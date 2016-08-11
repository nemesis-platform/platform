<?php

namespace NemesisPlatform\Components\Skins\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegistryPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('nemesis.skin_registry')) {
            return;
        }

        $registry = $container->getDefinition('nemesis.skin_registry');
        $themes   = $container->findTaggedServiceIds('theme');

        foreach ($themes as $id => $tags) {
            foreach ($tags as $tag) {
                $alias = array_key_exists('alias', $tag) ? $tag['alias'] : $id;
                $registry->addMethodCall('add', [$alias, new Reference($id)]);
            }
        }
    }
}
