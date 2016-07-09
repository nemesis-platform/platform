<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 25.02.2015
 * Time: 16:26
 */

namespace NemesisPlatform\Game\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RuleCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('nemesis.rule_registry')) {
            return;
        }

        $definition = $container->getDefinition('nemesis.rule_registry');

        $menuServices = $container->findTaggedServiceIds('rule_type');

        foreach ($menuServices as $id => $tagAttributes) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
