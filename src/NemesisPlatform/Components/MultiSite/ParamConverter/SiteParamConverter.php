<?php

namespace NemesisPlatform\Components\MultiSite\ParamConverter;

use NemesisPlatform\Components\MultiSite\Entity\MultiSiteElement;
use NemesisPlatform\Components\MultiSite\Service\SiteProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteParamConverter implements ParamConverterInterface
{
    /** @var  SiteProviderInterface */
    private $provider;
    /** @var ParamConverterInterface */
    private $converter;

    /**
     * SiteParamConverter constructor.
     *
     * @param ParamConverterInterface $converter
     * @param SiteProviderInterface   $provider
     */
    public function __construct(ParamConverterInterface $converter, SiteProviderInterface $provider)
    {
        $this->converter = $converter;
        $this->provider  = $provider;
    }

    /**
     * {@inheritdoc}
     * @throws NotFoundHttpException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {

        $name  = $configuration->getName();
        $class = $configuration->getClass();

        $site = $this->provider->getSite();

        if ($this->converter->apply($request, $configuration) === false) {
            return false;
        }

        $object = $request->attributes->get($name);

        if (!$object instanceof MultiSiteElement) {
            return false;
        }

        if ($object instanceof MultiSiteElement && !$object->belongsToSite($site)) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool    True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return
            $this->converter->supports($configuration) ||
            in_array(
                MultiSiteElement::class,
                class_implements($configuration->getClass()),
                true
            );
    }
}
