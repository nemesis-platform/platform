<?php

use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel for SensioLabs Insight inspections
 */
class AppKernel extends \NemesisPlatform\AppKernel
{
    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(dirname(__DIR__).'/app/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getRootDir()
    {
        return dirname(__DIR__).'/app';
    }
}
