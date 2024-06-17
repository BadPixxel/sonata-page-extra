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

namespace BadPixxel\SonataPageExtra\Page;

use BadPixxel\SonataPageExtra\Helpers\RequestParser;
use BadPixxel\SonataPageExtra\Interfaces\PageConfiguratorInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * Sonata Pages Seo Canonical Url Configurator
 */
#[AutoconfigureTag(PageConfiguratorInterface::TAG)]
class PageCanonicalConfigurator implements PageConfiguratorInterface
{
    /**
     * Page Configurator Constructor.
     */
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ?SeoPageInterface $seoPage = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handle(PageInterface $page): bool
    {
        return !empty($this->seoPage);
    }

    /**
     * @inheritDoc
     */
    public function configure(PageInterface $page, Request $request, array $parameters = array()): bool
    {
        $this->configureCanonical($request);

        return true;
    }

    /**
     * Configure the SEO Page Canonical Url.
     */
    private function configureCanonical(Request $request): void
    {
        Assert::notEmpty($this->seoPage);
        //==============================================================================
        // Inject Page Canonical Url if Needed
        if (!$canonicalUrl = $this->seoPage->getLinkCanonical()) {
            $canonicalUrl = $this->router->generate(
                RequestParser::getRoute($request),
                RequestParser::getRouteParams($request),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $this->seoPage->setLinkCanonical($canonicalUrl);
        }
        //==============================================================================
        // Push to Og Property
        $this->seoPage->addMeta('property', 'og:url', $canonicalUrl);
    }
}
