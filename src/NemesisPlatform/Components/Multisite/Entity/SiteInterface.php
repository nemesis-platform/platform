<?php

namespace NemesisPlatform\Components\MultiSite\Entity;

use Symfony\Component\HttpFoundation\Request;

interface SiteInterface
{
    /** @return string base URL */
    public function getDomain();

    /** @return string|null favicon URL */
    public function getFavicon();

    /** @return string|null Logo URL */
    public function getLogo();

    /** @return string long name of the site */
    public function getFullName();

    /** @return string short name of the site */
    public function getShortName();

    /** @return string|null contact email */
    public function getContactEmail();

    /**
     * @param Request $request
     *
     * @return bool whether the site matches request
     */
    public function match(Request $request);
}
