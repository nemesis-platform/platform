<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 13.04.2015
 * Time: 12:45
 */

namespace NemesisPlatform\Core\Account\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MaintenanceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('site.manager')) {
            return;
        }

        $container->getDefinition('site.manager')->replaceArgument(2, new Reference('nemesis.fallback_site_factory'));
    }
}
