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

namespace BadPixxel\SonataPageExtra\Services\Switcher;

use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Request\SiteRequestContextInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * Switch Symfony Router Context to Different Site/Hosts Contexts
 */

class RouteContextSwitcher
{
    /**
     * Currently Used Request Context Host
     */
    private ?string $currentHost = null;

    /**
     * Currently Used Website
     */
    private ?SiteInterface $currentSite = null;

    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * Change Current Website & Return Default
     */
    public function switchTo(SiteInterface $site): void
    {
        //==============================================================================
        // Backup Initial Context
        $context = $this->router->getContext();
        //==============================================================================
        // Force Context
        if ($context instanceof SiteRequestContextInterface) {
            $this->currentSite = $context->getSite();
            $context->setSite($site);
        } else {
            $this->currentHost = $context->getHost();
            $context->setHost((string) $site->getHost());
        }
    }

    /**
     * Restore Current Website Context
     */
    public function reset(): void
    {
        $context = $this->router->getContext();
        //====================================================================//
        // Reset Router Context
        if ($context instanceof SiteRequestContextInterface) {
            Assert::notEmpty($this->currentSite);
            $context->setSite($this->currentSite);
            $this->currentSite = null;
        } else {
            Assert::notEmpty($this->currentHost);
            $context->setHost($this->currentHost);
            $this->currentHost = null;
        }
    }
}
