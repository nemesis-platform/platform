<?php

namespace NemesisPlatform;

use NemesisPlatform\Admin\AdminBundle;
use NemesisPlatform\Components\ExportImport\ExportImportBundle;
use NemesisPlatform\Components\Form\Extensions\ExtensionsBundle;
use NemesisPlatform\Components\Form\PersistentForms\PersistentFormsBundle;
use NemesisPlatform\Components\Form\Survey\SurveyBundle;
use NemesisPlatform\Components\MultiSite\MultiSiteBundle;
use NemesisPlatform\Components\Themes\SwitchableThemeBundle;
use NemesisPlatform\Core\Account\CoreBundle;
use NemesisPlatform\Core\CMS\CMSBundle;
use NemesisPlatform\Game\GameBundle;
use ScayTrase\SmsDeliveryBundle\SmsDeliveryBundle;
use ScayTrase\WebSmsBridge\WebSmsBridgeBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // Core internals
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new \Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new SmsDeliveryBundle(),
            new WebSmsBridgeBundle(),

            // Nemesis core
            new MultiSiteBundle(),
            new PersistentFormsBundle(),
            new SwitchableThemeBundle(),

            new CoreBundle(),
            new CMSBundle(),

            // Nemesis modules
            new GameBundle(),
            new AdminBundle(),
            new ExportImportBundle(),
            new ExtensionsBundle(),
            new SurveyBundle(),

            // Built-ins
            new \NemesisPlatform\Modules\Game\Core\NemesisGameCoreBundle(),
            new \NemesisPlatform\Modules\Game\QAGame\QAGameBundle(),

        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

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
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getRootDir()
    {
        return __DIR__;
    }
}
