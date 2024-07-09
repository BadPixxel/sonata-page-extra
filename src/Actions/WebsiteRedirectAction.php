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

namespace BadPixxel\SonataPageExtra\Actions;

use BadPixxel\SonataPageExtra\Services\WebsiteSwitcher;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\SiteManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/**
 * Sonata Website Redirections Controller
 * Extends Generic redirection Controller to Force Website Url
 */
class WebsiteRedirectAction
{
    public function __construct(
        private readonly SiteManagerInterface $siteManager,
        private readonly WebsiteSwitcher $websiteSwitcher,
        private readonly RedirectController $redirectController,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        //==============================================================================
        // Get Route Parameters
        $routeParams = $request->attributes->get('_route_params', array());
        Assert::isArray($routeParams);
        Assert::keyExists($routeParams, 'site');
        //==============================================================================
        // Find Target Website
        $site = $this->siteManager->find($routeParams['site']);
        Assert::isInstanceOf($site, SiteInterface::class);
        //==============================================================================
        // Switch Router this Website
        $this->websiteSwitcher->switchTo($site);
        //==============================================================================
        // Remove Site option from Parameters
        unset($routeParams['site']);
        $request->attributes->set('_route_params', $routeParams);
        //==============================================================================
        // Generate Redirect Response
        $response = $this->redirectController->__invoke($request);
        //==============================================================================
        // Reset Router top Current Website
        $this->websiteSwitcher->switchTo($site);

        return $response;
    }
}
