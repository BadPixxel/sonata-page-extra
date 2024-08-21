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
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * Switch Symfony Router Context to Different Site/Hosts Contexts
 */

class RouteContextSwitcher
{
    /**
     * Currently Used Website
     */
    private ?RequestContext $currentContext = null;

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
        $this->currentContext = $context;
        //==============================================================================
        // Force Context
        if ($context instanceof SiteRequestContextInterface) {
            $context->setSite($site);
        } else {
            $context->setHost((string) $site->getHost());
        }
    }

    /**
     * Restore Current Website Context
     */
    public function reset(): void
    {
        Assert::notEmpty($this->currentContext);
        //====================================================================//
        // Reset Router Context
        $this->router->setContext($this->currentContext);
        $this->currentContext = null;
    }
}
