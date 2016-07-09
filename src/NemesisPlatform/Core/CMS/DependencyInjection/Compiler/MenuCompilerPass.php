<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-08-17
 * Time: 22:14
 */

namespace NemesisPlatform\Core\CMS\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MenuCompilerPass implements CompilerPassInterface
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
        if (!$container->hasDefinition('nemesis.registry.menu')) {
            return;
        }

        $defenition = $container->getDefinition('nemesis.registry.menu');

        $menuServices = $container->findTaggedServiceIds('menu_element');

        foreach ($menuServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $defenition->addMethodCall('add', [new Reference($id), $attributes["menu"]]);
            }
        }
    }
}
