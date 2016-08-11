<?php

namespace NemesisPlatform\Components\Skins;

use NemesisPlatform\Components\Skins\Service\CompilableThemeInterface;
use NemesisPlatform\Components\Skins\Service\LayoutStorageInterface;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractBootstrapTheme implements LayoutStorageInterface, CompilableThemeInterface
{
    /** @var  \Twig_Environment */
    protected $twig;

    public function __construct(\Twig_Environment $twig)
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
