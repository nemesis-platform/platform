<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 07.07.2015
 * Time: 11:43
 */

namespace NemesisPlatform\Core\CMS\Theme;

use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\Skins\AbstractConfigurableBootstrapTheme;
use Symfony\Bundle\AsseticBundle\Factory\AssetFactory;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class NemesisBootstrapTheme extends AbstractConfigurableBootstrapTheme implements FormInjectorInterface
{

    /** @var  FileLocatorInterface */
    protected $locator;
    /** @var  string */
    protected $kernelRootDir;

    public function __construct(
        \Twig_Environment $twig,
        AssetFactory $factory,
        $writeTo,
        FileLocatorInterface $locator,
        $kernelRootDir
    ) {
        parent::__construct($twig, $factory, $writeTo);
        $this->locator = $locator;
        $this->kernelRootDir = $kernelRootDir;
    }

    public function injectForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add(
            'variables',
            'key_value_collection',
            [
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => ['style' => 'inline'],
            ]
        )
            ->add(
                'lessfile',
                'text',
                [
                    'empty_data' => substr(sha1(uniqid('_theme', true)), 0, 6),
                    'attr' => ['help_text' => 'Уникальный код для формирования файла стиля'],
                ]
            )
            ->add(
                'contacts_path',
                'text',
                [
                    'required' => false,
                    'attr' => ['help_text' => 'Альяс страницы контактов'],
                ]
            );
    }

    /**
     * @param string $layout
     *
     * @return string
     */
    public function get($layout = 'base')
    {
        if (array_key_exists($layout, $this->getLayouts())) {
            return $this->getLayouts()[$layout];
        }

        return $this->getBaseLayout();
    }

    /**
     * @return string[]
     */
    abstract protected function getLayouts();

    /** @return string */
    abstract protected function getBaseLayout();

    /** @return string[] */
    public function all()
    {
        return $this->getLayouts();
    }

    protected function getAssetsDir()
    {
        return $this->locator->locate($this->kernelRootDir.'/../vendor/npm-asset/bootstrap');
    }
}
