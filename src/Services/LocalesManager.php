<?php

/*
 *  Copyright (C) BadPixxel <www.badpixxel.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace BadPixxel\SonataPageExtra\Services;

use BadPixxel\SonataPageExtra\Helpers\RequestParser;
use Sonata\PageBundle\CmsManager\CmsManagerInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Request;

/**
 * Sonata Pages Locales Manager
 */
#[Autoconfigure(bind: array(
    '$cmsPageManager' => "@sonata.page.cms.page",
))]
class LocalesManager
{
    /**
     * Page Configurator Constructor.
     */
    public function __construct(
        private readonly WebsiteManager    $hostsManager,
        private readonly CmsManagerInterface $cmsPageManager
    ) {
    }

    /**
     * Get List of All Available Languages
     *
     * @return string[]
     */
    public function getAll(): array
    {
        return array_keys($this->getAllWebsites());
    }

    /**
     * Get List of All Available Websites by Languages
     *
     * @return array<string, SiteInterface>
     */
    public function getAllWebsites(): array
    {
        static $locales;

        if (!isset($locales)) {
            $locales = array();
            foreach ($this->hostsManager->getAvailableSites() as $site) {
                $locales[$site->getLocale()] ??= $site;
            }
        }

        return $locales;
    }

    /**
     * Get Lang Alternates for a Page.
     */
    public function getLocaleAlternates(Request $request): array
    {
        //==============================================================================
        // Safety Check
        if (!$page = $this->cmsPageManager->getCurrentPage()) {
            $langAlternates = array();
            foreach ($this->getAllWebsites() as $locale => $site) {
                //==============================================================================
                // Generate Localized Site Url
                $localeUrl = sprintf(
                    "%s://%s%s",
                    $request->getScheme(),
                    $site->getHost(),
                    $site->getRelativePath(),
                );
                //==============================================================================
                // Add Localized Site Url if no Exists
                if (!isset($langAlternates[$locale])) {
                    $langAlternates[$locale] = $localeUrl;
                }
            }

            return $langAlternates;
        }
        //==============================================================================
        // Lang Alternates Already Configured
        $langAlternates = array();
        foreach ($this->getAllWebsites() as $locale => $site) {
            //==============================================================================
            // Generate Localized Site Url
            $localeUrl = $this->getUrlForWebsite($site, $page, $request);
            //==============================================================================
            // Add Localized Site Url if no Exists
            if (!isset($langAlternates[$locale])) {
                $langAlternates[$locale] = $localeUrl;
            }
        }

        return $langAlternates;
    }

    /**
     * Get Lang Alternates for a Page.
     */
    public function getLangAlternates(PageInterface $page, Request $request): array
    {
        //==============================================================================
        // Load Current Page Parent Site
        if (!$page->getSite()) {
            return array();
        }
        //==============================================================================
        // Lang Alternates Already Configured
        $langAlternates = array();
        foreach ($this->getAllWebsites() as $locale => $site) {
            //==============================================================================
            // Generate Localized Site Url
            $localeUrl = $this->getUrlForWebsite($site, $page, $request);
            //==============================================================================
            // Add Localized Site Url if no Exists
            if (!isset($langAlternates[$localeUrl])) {
                $langAlternates[$localeUrl] = $locale;
            }
        }

        return $langAlternates;
    }

    /**
     * Get Lang Alternates for a Page.
     */
    private function getUrlForWebsite(SiteInterface $site, PageInterface $page, Request $request): string
    {
        //==============================================================================
        // If Page is Hybrid Page
        if ($page->isHybrid() || $page->isDynamic()) {
            return $this->hostsManager->generateSiteUrl(
                $site,
                (string) $page->getRouteName(),
                RequestParser::getRouteParams($request)
            );
        }
        //==============================================================================
        // If Page has technical alias => Use it !
        if ($alias = $page->getPageAlias()) {
            return $this->hostsManager->generateSiteUrl(
                $site,
                $alias,
                RequestParser::getRouteParamsNoPath($request)
            );
        }

        //==============================================================================
        // Generate Localized Site Url
        return $this->hostsManager->generateSiteUrl(
            $site,
            RequestParser::getRoute($request),
            RequestParser::getRouteParams($request)
        );
    }
}
