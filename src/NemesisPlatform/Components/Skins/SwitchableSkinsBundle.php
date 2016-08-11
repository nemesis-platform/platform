<?php

namespace NemesisPlatform\Components\Skins;

use NemesisPlatform\Components\Skins\DependencyInjection\Compiler\RegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SwitchableSkinsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegistryPass());
    }
}
