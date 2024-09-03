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

use BadPixxel\SonataPageExtra\Services\SitemapsPathBuilder;
use BadPixxel\SonataPageExtra\Services\WebsiteManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/**
 * Serve Sitemap with host selection
 */
class Sitemap extends AbstractController
{
    public function __construct(
        private readonly SitemapsPathBuilder $pathBuilder,
        private readonly WebsiteManager      $hostsManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        //==============================================================================
        // Check if Sonata Page Site Selected
        $site = $this->hostsManager->getCurrentSite();
        //==============================================================================
        // Search for First Site with This Host
        if (!$site) {
            foreach ($this->hostsManager->getAvailableSites() as $availableSite) {
                if ($request->getHost() === $availableSite->getHost()) {
                    $site = $availableSite;
                }
            }
        }
        //==============================================================================
        // Ensure Sonata Page Site Selected
        Assert::notEmpty($site);
        //==============================================================================
        // Ensure Host Sitemap Storage path Exists
        Assert::readable($path = $this->pathBuilder->getFinalAbsolutePath($site->getHost()));

        return new BinaryFileResponse($path);
    }
}
