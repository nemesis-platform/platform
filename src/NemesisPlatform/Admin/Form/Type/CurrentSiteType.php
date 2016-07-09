<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 28.05.2015
 * Time: 17:15
 */

namespace NemesisPlatform\Admin\Form\Type;

use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Components\MultiSite\Service\SiteManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CurrentSiteType extends AbstractType
{
    /** @var  SiteManagerInterface */
    private $siteManager;

    /**
     * CurrentSiteType constructor.
     *
     * @param SiteManagerInterface $siteManager
     */
    public function __construct(SiteManagerInterface $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    public function getParent()
    {
        return 'entity';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'class'     => SiteInterface::class,
                'read_only' => true,
                'required'  => true,
                'label'     => 'Сайт',
                'data'      => $this->siteManager->getSite(),
            ]
        );
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'current_site';
    }
}
