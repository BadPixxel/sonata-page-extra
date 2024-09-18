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

namespace BadPixxel\SonataPageExtra\Phpunit;

use Sonata\PageBundle\Request\SiteRequest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Request;

/**
 * Client simulates a browser and makes requests to an HttpKernel instance.
 * Overridden to provide Sonata Site Request for testing
 */
class HttpSiteBrowser extends KernelBrowser
{
    /**
     * Converts the BrowserKit request to a Sonata Site Request.
     *
     * @param Request $request
     *
     * @return SiteRequest Sonata Site Request instance
     */
    protected function filterRequest(Request $request): SiteRequest
    {
        return SiteRequest::create(
            $request->getUri(),
            $request->getMethod(),
            $request->getParameters(),
            $request->getCookies(),
            $request->getFiles(),
            $request->getServer(),
            $request->getContent()
        );
    }
}
