<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-07-07
 * Time: 21:57
 */

namespace NemesisPlatform\Core\Account;

use NemesisPlatform\Game\DependencyInjection\Compiler\RuleCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RuleCompilerPass());
    }
}
