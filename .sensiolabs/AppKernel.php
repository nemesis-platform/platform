<?php

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

    public function getRootDir()
    {
        return dirname(__DIR__).'/app';
    }
}
