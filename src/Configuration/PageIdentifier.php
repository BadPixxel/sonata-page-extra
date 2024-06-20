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

namespace BadPixxel\SonataPageExtra\Configuration;

use BadPixxel\SonataPageExtra\Services\WebsiteSwitcher;
use Sonata\PageBundle\CmsManager\CmsPageManager;
use Sonata\PageBundle\Exception\PageNotFoundException;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * Identify Sonata Page to Configure
 */
#[Autoconfigure(bind: array(
    '$cmsPageManager' => "@sonata.page.cms.page",
))]
class PageIdentifier
{
    public function __construct(
        private readonly CmsPageManager $cmsPageManager,
        private readonly WebsiteSwitcher $websiteSwitcher,
    ) {
    }

    /**
     * Identify Page from Configuration Key.
     */
    public function identify(SiteInterface $site, string $key): ?PageInterface
    {
        //==============================================================================
        // Ensure Page resolution on Website
        $this->websiteSwitcher->switchTo($site);

        //==============================================================================
        // Identify by Page Alias
        try {
            $page = $this->cmsPageManager->getPageByPageAlias($site, $key);
        } catch (PageNotFoundException) {
            $page = null;
        }

        //==============================================================================
        // Identify by Page Route Name
        try {
            $page ??= $this->cmsPageManager->getPageByRouteName($site, $key);
        } catch (PageNotFoundException) {
            $page = null;
        }

        //==============================================================================
        // Identify by Page ID
        try {
            if (is_numeric($key)) {
                $page ??= $this->cmsPageManager->getPageById($key);
            }
        } catch (PageNotFoundException) {
            $page = null;
        }
        //==============================================================================
        // Reset Website
        $this->websiteSwitcher->reset();

        return $page;
    }
}
