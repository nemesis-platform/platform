<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-09
 * Time: 16:34
 */

namespace NemesisPlatform\Components\Test\Testing;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class KernelForTest extends Kernel
{
    /** @var Bundle[] */
    private $additional_bundles = [];

    private $additional_configs = [];

    /**
     * @param string   $environment
     * @param bool     $debug
     * @param Bundle[] $additional_bundles
     * @param array    $additional_configs
     */

    public function __construct($environment, $debug, $additional_bundles = [], $additional_configs = [])
    {
        $this->additional_bundles = $additional_bundles;
        $this->additional_configs = array_merge(
            [
                __DIR__.'/config.yml',
            ],
            $additional_configs
        );
        parent::__construct($environment, $debug);
    }

    /** {@inheritdoc} */
    public function registerBundles()
    {
        return array_merge(
            [
                new FrameworkBundle(),
                new SecurityBundle(),
                new TwigBundle(),
                new DoctrineBundle(),
            ],
            $this->additional_bundles
        );
    }

    /** {@inheritdoc} */
    public function getCacheDir()
    {
        return defined('CACHE_DIR') ? CACHE_DIR : getcwd().'/build/var/cache/'.$this->getEnvironment();
    }

    /** {@inheritdoc} */
    public function getLogDir()
    {
        return defined('LOGS_DIR') ? LOGS_DIR : getcwd().'/build/var/logs/';
    }

    /** {@inheritdoc} */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        foreach ($this->additional_configs as $config) {
            $loader->load($config);
        }
    }

    protected function getKernelParameters()
    {
        return array_merge(
            parent::getKernelParameters(),
            [
                'secret' => 'FixtureCsrfSecret',
            ]
        );
    }
}
