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

use BadPixxel\SonataPageExtra\Route\SiteRouteGenerator;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\SiteManagerInterface;
use Sonata\PageBundle\Site\HostByLocaleSiteSelector;
use Sonata\PageBundle\Site\HostPathByLocaleSiteSelector;
use Sonata\PageBundle\Site\SiteSelectorInterface;
use Webmozart\Assert\Assert;

/**
 * Top Level Websites Manager
 */
class WebsiteManager
{
    /**
     * Static Instance for Access Anywhere
     */
    private static WebsiteManager $staticInstance;

    /**
     * Page Configurator Constructor.
     */
    public function __construct(
        private readonly SiteSelectorInterface  $siteSelector,
        private readonly SiteManagerInterface   $siteManager,
        private readonly SiteRouteGenerator $siteRouteGenerator,
    ) {
        //==============================================================================
        // Store Static Instance for Access as Static
        self::$staticInstance = $this;
    }

    /**
     * Static Access to this Service.
     */
    public static function getInstance(): WebsiteManager
    {
        return self::$staticInstance;
    }

    /**
     * @return array<string, SiteInterface>
     */
    public function getAvailableSites(): array
    {
        static $websites;

        return $websites ??= $this->siteManager->findBy(array(
            'enabled' => true,
        ));
    }

    /**
     * Get List of All Websites Names
     *
     * @return array<string, string>
     */
    public function getSitesNames(): array
    {
        static $names;

        if (!isset($names)) {
            $names = array();
            foreach ($this->getAvailableSites() as $site) {
                if ($name = $site->getName()) {
                    $names[$name] = $name;
                }
            }
        }

        return $names;
    }

    /**
     * Get List of All Websites Urls
     *
     * @return array<string, string>
     */
    public function getSitesHosts(): array
    {
        static $hosts;

        if (!isset($hosts)) {
            $hosts = array();
            foreach ($this->getAvailableSites() as $site) {
                $hosts[$site->getName()] = $site->getHost();
            }
        }

        return $hosts;
    }

    /**
     * Get List of All Websites Groped by Host
     *
     * @return array<string, array<string, SiteInterface>>
     */
    public function getSitesGroups(): array
    {
        static $groups;

        if (!isset($groups)) {
            $groups = array();
            foreach ($this->getAvailableSites() as $site) {
                Assert::notEmpty($host = $site->getHost());
                Assert::notEmpty($name = $site->getName());
                $groups[$host] ??= array();
                $groups[$host][$name] = $site;
            }
        }

        return $groups;
    }

    /**
     * Get Site by Name
     */
    public function getSite(string $siteName): SiteInterface
    {
        foreach ($this->getAvailableSites() as $site) {
            if ($site->getName() === $siteName) {
                return $site;
            }
        }

        throw new \LogicException(sprintf("Website %s not found!", $siteName));
    }

    /**
     * Get Host Url for Site Name
     *
     * @param null|string $siteName
     *
     * @return null|string
     */
    public function getSiteHost(?string $siteName): ?string
    {
        $siteName = $siteName ?? $this->getCurrentSiteName();
        $urls = $this->getSitesHosts();

        return $urls[$siteName] ?? null;
    }

    /**
     * Get Current Site Name
     */
    public function getCurrentSiteName(): ?string
    {
        static $current;

        $current ??= $this->getCurrentSite()?->getName();
        $current ??= $this->getDefaultSite()->getName();

        return $current;
    }

    /**
     * Get Current Site
     */
    public function getCurrentSite(): ?SiteInterface
    {
        static $current;

        return $current ??= $this->siteSelector->retrieve();
    }

    /**
     * Get Default Site
     */
    public function getDefaultSite(): SiteInterface
    {
        static $default;

        if (!isset($default)) {
            foreach ($this->getAvailableSites() as $site) {
                if ($site->getIsDefault()) {
                    $default = $site;
                }
            }
            Assert::notEmpty($default, "You must select a default website !");
        }

        return $default;
    }

    /**
     * Generates a Site Absolute URL from the given parameters.
     */
    public function generateSiteUrl(string|SiteInterface $site, string $path, array $params = array()): string
    {
        //==============================================================================
        // Parse Inputs
        $site = (!$site instanceof SiteInterface) ? $this->getSite($site) : $site;

        return $this->siteRouteGenerator->generateSiteUrl($site, $path, $params);
    }

    /**
     * Generates a Site Relative Path from the given parameters.
     */
    public function generateSitePath(string|SiteInterface $site, string $path, array $params = array()): string
    {
        //==============================================================================
        // Parse Inputs
        $site = (!$site instanceof SiteInterface) ? $this->getSite($site) : $site;

        return $this->siteRouteGenerator->generateSitePath($site, $path, $params);
    }

    /**
     * We are in Multi-lang Mode.
     */
    public function isMultiLang(): bool
    {
        return ($this->siteSelector instanceof HostPathByLocaleSiteSelector)
            || ($this->siteSelector instanceof HostByLocaleSiteSelector)
        ;
    }
}
