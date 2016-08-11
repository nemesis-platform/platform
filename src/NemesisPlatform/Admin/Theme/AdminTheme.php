<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 11.03.2015
 * Time: 14:14
 */

namespace NemesisPlatform\Admin\Theme;

use NemesisPlatform\Core\CMS\Entity\Block\AreaProviderInterface;
use NemesisPlatform\Core\CMS\Theme\NemesisBootstrapTheme;
use Symfony\Component\Filesystem\Filesystem;

class AdminTheme extends NemesisBootstrapTheme implements AreaProviderInterface
{
    private $layouts = ['base' => 'NemesisAdminBundle:Theme\Admin:base.html.twig',];
    private $areas   = ['dashboard_c1', 'dashboard_c2', 'dashboard_c3'];

    /**
     * @return string[]
     */
    public function getAreas()
    {
        return $this->areas;
    }

    protected function getVariablesFile()
    {
        return $this->locator->locate('@NemesisAdminBundle/Resources/public/theme/admin/less/variables.less');
    }

    protected function getBootstrapTemplate()
    {
        return 'NemesisAdminBundle:Theme\Admin:bootstrap.less.twig';
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
                $this->kernelRootDir.'/../vendor/npm-asset/bootstrap-switch/src/less/bootstrap3/bootstrap-switch.less'
            )
        );


        $switchLessDir = $fs->makePathRelative(
            $bootstrap_switch,
            $lessfile
        );

        $jasny_bootstrap = realpath(
            dirname(
                $this->kernelRootDir
                .'/../vendor/npm-asset/jasny-bootstrap/less/jasny-bootstrap.less'
            )
        );
        $jasnyDir        = $fs->makePathRelative(
            $jasny_bootstrap,
            $lessfile
        );


        return array_merge_recursive(
            parent::getCompilationOptions(),
            [
                'bootstrap_switch_less' => $switchLessDir.'bootstrap-switch.less',
                'jasny_dir' => $jasnyDir,
            ]
        );
    }

    protected function getBootstrapLessFile()
    {
        return $this->kernelRootDir.'/../web/less/admin_bootstrap_theme.less';
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

    protected function getConfigurationLessTemplate()
    {
        return 'NemesisAdminBundle:Theme/Admin:configuration.less.twig';
    }
}
