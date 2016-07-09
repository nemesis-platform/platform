<?php
namespace NemesisPlatform\Core\CMS\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BlockCompilerPass implements CompilerPassInterface
{

    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('nemesis.block_registry')) {
            return;
        }

        $registry = $container->getDefinition('nemesis.block_registry');
        $blocks   = $container->findTaggedServiceIds('block_type');

        foreach ($blocks as $id => $tags) {
            foreach ($tags as $tag) {
                $alias = array_key_exists('alias', $tag) ? $tag['alias'] : $id;
                $registry->addMethodCall('add', [$alias, new Reference($id)]);
            }
        }
    }
}
