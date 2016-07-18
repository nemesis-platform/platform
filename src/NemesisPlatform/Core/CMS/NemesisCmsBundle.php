<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-07-07
 * Time: 21:43
 */

namespace NemesisPlatform\Core\CMS;

use NemesisPlatform\Core\CMS\DependencyInjection\Compiler\BlockCompilerPass;
use NemesisPlatform\Core\CMS\DependencyInjection\Compiler\MenuCompilerPass;
use NemesisPlatform\Core\Account\DependencyInjection\Compiler\MaintenanceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NemesisCmsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MenuCompilerPass());
        $container->addCompilerPass(new BlockCompilerPass());
        $container->addCompilerPass(new MaintenanceCompilerPass());
    }
}
