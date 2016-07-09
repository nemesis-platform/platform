<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 15.01.2015
 * Time: 15:31
 */

namespace NemesisPlatform\Components\Themes;

use Assetic\Asset\AssetCollection;
use NemesisPlatform\Components\Themes\Service\ConfigurableThemeInterface;
use Symfony\Bundle\AsseticBundle\Factory\AssetFactory;

abstract class AbstractConfigurableBootstrapTheme extends AbstractBootstrapTheme implements ConfigurableThemeInterface
{
    /** @var  AssetCollection */
    protected $asset;
    /** @var */
    protected $configuration = array();

    /** @var  AssetFactory */
    protected $factory;
    /** @var  string */
    protected $writeTo;

    /**
     * AbstractConfigurableBootstrapTheme constructor.
     *
     * @param \Twig_Environment $twig
     * @param AssetFactory      $factory
     * @param string            $writeTo
     */
    public function __construct(\Twig_Environment $twig, AssetFactory $factory, $writeTo)
    {
        parent::__construct($twig);
        $this->factory       = $factory;
        $this->writeTo       = $writeTo;
        $this->configuration = array(
            'lessfile'  => 'zeroconfig',
            'variables' => array(),
        );
    }

    public function getCssFile()
    {
        $asset = $this->getAsset();

        return $asset->getTargetPath();
    }

    /**
     * @return AssetCollection
     */
    protected function getAsset()
    {
        if (!$this->asset) {
            $inputs  = array($this->getBootstrapLessFile());
            $filters = array('lessphp');
            $options = array('output' => 'css/*.css');

            $this->asset = $this->factory->createAsset($inputs, $filters, $options);
        }

        return $this->asset;
    }

    protected function getBootstrapLessFile()
    {
        return $this->writeTo.'/less/'.$this->getType().'_'.$this->configuration['lessfile'].'.less';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'basic_bootstrap_theme';
    }

    public function compile()
    {
        /** Render configuration first  */
        file_put_contents(
            $this->getConfigurationLessFile(),
            $this->twig->render($this->getConfigurationLessTemplate(), $this->getCompilationOptions())
        );

        parent::compile();

        /*
         * As soon as dynamically called less files is never compiled with tools such as assetic by default
         * It is preferred to manually compile the source less to end-user code
         */
        $asset = $this->getAsset();
        file_put_contents(
            $this->writeTo.'/'.$asset->getTargetPath(),
            $asset->dump()
        );
    }

    protected function getConfigurationLessFile()
    {
        return $this->writeTo.'/less/'.$this->getType().'_'.$this->configuration['lessfile'].'_configuration.less';
    }

    abstract protected function getConfigurationLessTemplate();

    protected function getCompilationOptions()
    {
        return array_merge_recursive(
            parent::getCompilationOptions(),
            array(
                'configuration' => $this->getConfiguration(),
            )
        );
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function setConfiguration($config)
    {
        $this->configuration = array_replace_recursive($this->configuration, $config);
    }
}
