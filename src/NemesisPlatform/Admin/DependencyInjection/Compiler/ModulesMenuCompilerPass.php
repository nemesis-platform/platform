<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-08-17
 * Time: 22:14
 */

namespace NemesisPlatform\Admin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ModulesMenuCompilerPass implements CompilerPassInterface
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
        if (!$container->hasDefinition('admin_menu.modules_menu')) {
            return;
        }

        $definition = $container->getDefinition('admin_menu.modules_menu');

        $menuServices = $container->findTaggedServiceIds('game_module_menu');

        foreach ($menuServices as $id => $tagAttributes) {
            $definition->addMethodCall('addChild', [new Reference($id)]);
        }
    }
}
