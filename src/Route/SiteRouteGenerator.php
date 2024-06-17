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

namespace BadPixxel\SonataPageExtra\Route;

use BadPixxel\SonataPageExtra\Services\WebsiteSwitcher;
use Sonata\PageBundle\Model\SiteInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Generate Routes with Sonata Website Selection
 */
class SiteRouteGenerator
{
    public function __construct(
        private readonly WebsiteSwitcher $cmsSwitcher,
        private readonly RouterInterface $router
    ) {
    }

    /**
     * Generates a Site Absolute URL from the Given Parameters.
     */
    public function generateSiteUrl(SiteInterface $site, string $path, array $params = array()): string
    {
        return $this->generate($site, $path, $params, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Generates a Site Absolute URL from the Given Parameters.
     */
    public function generateSitePath(SiteInterface $site, string $path, array $params = array()): string
    {
        return $this->generate($site, $path, $params, UrlGeneratorInterface::RELATIVE_PATH);
    }

    /**
     * Generates a Site Absolute URL from the given parameters.
     */
    private function generate(SiteInterface $site, string $path, array $params = array(), int $type = null): string
    {
        //==============================================================================
        // Force Context
        $this->cmsSwitcher->switchTo($site);
        //==============================================================================
        // Generate url
        $url = $this->router->generate($path, $params, $type ?? UrlGeneratorInterface::RELATIVE_PATH);
        //==============================================================================
        // Restore Context Host
        $this->cmsSwitcher->reset();

        return $url;
    }
}
