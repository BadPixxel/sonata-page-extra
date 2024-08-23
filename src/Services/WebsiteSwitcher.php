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

use Sonata\PageBundle\Model\SiteInterface;

/**
 * Enable Using Sonata Page Manager with Different Site/Hosts Contexts
 */

class WebsiteSwitcher
{
    public function __construct(
        private readonly Switcher\SiteSelectorSwitcher   $siteSelectorSwitcher,
        private readonly Switcher\CmsPageManagerSwitcher $cmsManagerPageSwitcher,
        private readonly Switcher\CmsSnapshotManagerSwitcher $cmsManagerSnapshotSwitcher,
        private readonly Switcher\RouteContextSwitcher $routeContextSwitcher
    ) {
    }

    /**
     *
     */
    public function switchTo(SiteInterface $site): void
    {
        //====================================================================//
        // Change Site Selector Website
        $currentSite = $this->siteSelectorSwitcher->switchTo($site);
        //====================================================================//
        // Change Cms Manager Page References for Website
        $this->cmsManagerPageSwitcher->setDefaultSite($currentSite)->switchTo($site);
        $this->cmsManagerSnapshotSwitcher->setDefaultSite($currentSite)->switchTo($site);
        //====================================================================//
        // Change Symfony Router Context
        $this->routeContextSwitcher->switchTo($site);
    }

    /**
     * Reset Sonata Website Switcher Overrides
     */
    public function reset(): void
    {
        //====================================================================//
        // Reset Site Selector Website
        $this->siteSelectorSwitcher->reset();
        //====================================================================//
        // Reset Cms Manager Page References for Website
        $this->cmsManagerPageSwitcher->reset();
        $this->cmsManagerSnapshotSwitcher->reset();
        //====================================================================//
        // Reset Symfony Router Context
        $this->routeContextSwitcher->reset();
    }
}
