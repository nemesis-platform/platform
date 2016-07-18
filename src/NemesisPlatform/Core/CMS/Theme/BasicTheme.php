<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.01.2015
 * Time: 12:54
 */

namespace NemesisPlatform\Core\CMS\Theme;

use NemesisPlatform\Core\CMS\Entity\Block\AreaProviderInterface;
use Symfony\Component\Filesystem\Filesystem;

class BasicTheme extends NemesisBootstrapTheme implements AreaProviderInterface
{
    /** @var string[] */
    private $layouts
        = [
            'base'  => 'NemesisCmsBundle:Theme\Basic:base.html.twig',
            'front' => 'NemesisCmsBundle:Theme\Basic:front.html.twig',
        ];

    private $areas
        = ['footer', 'sidebar'];


    /**
     * @return string
     */
    public function getType()
    {
        return 'basic_bootstrap_theme';
    }

    /**
     * @return string[]
     */
    public function getAreas()
    {
        return $this->areas;
    }

    protected function getConfigurationLessTemplate()
    {
        return 'NemesisCmsBundle:Theme\Basic:configuration.less.twig';
    }

    protected function getCompilationOptions()
    {
        $fs = new Filesystem;

        $lesspath = dirname($this->getBootstrapLessFile());
        if (!is_dir($lesspath)) {
            mkdir($lesspath, 0777, true);
        }

        $lessfile         = realpath($lesspath);
        $bootstrap_switch = realpath(
            dirname(
                $this->kernelRootDir.'/../vendor/nostalgiaz/bootstrap-switch/src/less/bootstrap3/bootstrap-switch.less'
            )
        );

        $switchLessDir = $fs->makePathRelative(
            $bootstrap_switch,
            $lessfile
        );

        $jasny_bootstrap = realpath(
            dirname($this->kernelRootDir.'/../vendor/jasny/bootstrap/less/jasny-bootstrap.less')
        );

        $jasnyDir = $fs->makePathRelative(
            $jasny_bootstrap,
            $lessfile
        );


        $configLessFile = $fs->makePathRelative(
            realpath(dirname($this->getConfigurationLessFile())),
            realpath(dirname($this->getBootstrapLessFile()))
        );

        $configLessFile .= basename($this->getConfigurationLessFile());

        return array_merge_recursive(
            parent::getCompilationOptions(),
            [
                'bootstrap_switch_less' => $switchLessDir.'bootstrap-switch.less',
                'jasny_dir'             => $jasnyDir,
                'configuration_less'    => $configLessFile,
            ]
        );
    }

    protected function getVariablesFile()
    {
        return $this->locator->locate('@NemesisCmsBundle/Resources/public/theme/basic/variables.less');
    }

    protected function getBootstrapTemplate()
    {
        return 'NemesisCmsBundle:Theme\Basic:bootstrap.less.twig';
    }

    /**
     * @return string[]
     */
    protected function getLayouts()
    {
        return $this->layouts;
    }

    /** @return string */
    protected function getBaseLayout()
    {
        return $this->layouts['base'];
    }
}
