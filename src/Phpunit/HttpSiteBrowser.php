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
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Client simulates a browser and makes requests to an HttpKernel instance.
 * Overridden to provide Sonata Site Request for testing
 */
class HttpSiteBrowser extends KernelBrowser
{
    /**
     * Converts the BrowserKit request to a Sonata Site Request.
     *
     * @param BrowserKitRequest $request
     *
     * @return SiteRequest Sonata Site Request instance
     */
    protected function filterRequest(BrowserKitRequest $request): SiteRequest
    {
        static $configured;
        //==============================================================================
        // Ensure Configuration of Factory in case of Sub-Requests
        $configured ??= $this->configureFactory();

        //==============================================================================
        // Convert Native Request to Sonata Site Request
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

    /**
     * Configure Request Factory.
     */
    protected function configureFactory(): bool
    {
        Request::setFactory(
            /**
             * @param null|resource|string $content
             */
            static fn (
                array $query = array(),
                array $request = array(),
                array $attributes = array(),
                array $cookies = array(),
                array $files = array(),
                array $server = array(),
                $content = null
            ) => new SiteRequest($query, $request, $attributes, $cookies, $files, $server, $content)
        );

        return true;
    }
}
