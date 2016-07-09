<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 15.01.2015
 * Time: 15:31
 */

namespace NemesisPlatform\Components\Themes;

use NemesisPlatform\Components\Themes\Service\CompilableThemeInterface;
use NemesisPlatform\Components\Themes\Service\ThemeInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig_Environment;

abstract class AbstractBootstrapTheme implements ThemeInterface, CompilableThemeInterface
{
    /** @var  Twig_Environment */
    protected $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    abstract protected function getBootstrapLessFile();

    abstract protected function getVariablesFile();

    abstract protected function getAssetsDir();

    abstract protected function getBootstrapTemplate();

    protected function getCompilationOptions()
    {
        $fs = new Filesystem;
        $fs->mkdir(dirname($this->getBootstrapLessFile()));

        $assets_dir = $fs->makePathRelative(
            realpath($this->getAssetsDir()),
            realpath(dirname($this->getBootstrapLessFile()))
        );

        $variablesDir = $fs->makePathRelative(
            realpath(dirname($this->getVariablesFile())),
            realpath(dirname($this->getBootstrapLessFile()))
        );

        $variablesFile = sprintf(
            '%s%s',
            $variablesDir,
            basename($this->getVariablesFile())
        );

        return array(
            'variables_dir'  => $variablesDir,
            'variables_file' => $variablesFile,
            'assets_dir'     => $assets_dir,
        );
    }

    /**
     * @return bool
     */
    public function compile()
    {
        $content = $this->twig->render(
            $this->getBootstrapTemplate(),
            $this->getCompilationOptions()
        );

        file_put_contents($this->getBootstrapLessFile(), $content);

        return true;
    }
}
