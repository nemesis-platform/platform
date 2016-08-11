<?php

namespace NemesisPlatform\Components\Skins;

use Assetic\Asset\AssetCollection;
use NemesisPlatform\Components\Skins\Service\ConfigurableThemeInterface;
use Symfony\Bundle\AsseticBundle\Factory\AssetFactory;

abstract class AbstractConfigurableBootstrapTheme extends AbstractBootstrapTheme implements ConfigurableThemeInterface
{
    /** @var  AssetCollection */
    protected $asset;
    /** @var */
    protected $configuration = [];

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
        $this->configuration = [
            'lessfile'  => 'zeroconfig',
            'variables' => [],
        ];
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
            $inputs  = [$this->getBootstrapLessFile()];
            $filters = ['lessphp'];
            $options = ['output' => 'css/*.css'];

            $this->asset = $this->factory->createAsset($inputs, $filters, $options);
        }

        return $this->asset;
    }

    protected function getBootstrapLessFile()
    {
        return $this->writeTo.'/less/basic_bootstrap_theme_'.$this->configuration['lessfile'].'.less';
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
        return $this->writeTo.'/less/basic_bootstrap_theme_'.$this->configuration['lessfile'].'_configuration.less';
    }

    abstract protected function getConfigurationLessTemplate();

    protected function getCompilationOptions()
    {
        return array_merge_recursive(
            parent::getCompilationOptions(),
            [
                'configuration' => $this->configuration,
            ]
        );
    }

    public function configure(array $config)
    {
        $this->configuration = array_replace_recursive($this->configuration, $config);
    }
}
