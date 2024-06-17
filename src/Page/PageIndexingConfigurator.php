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

use BadPixxel\SonataPageExtra\Interfaces\PageConfiguratorInterface;
use BadPixxel\SonataPageExtra\Interfaces\SeoAwarePageInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

/**
 * Sonata Pages Seo Indexing Configurator
 */
#[AutoconfigureTag(PageConfiguratorInterface::TAG)]
#[Autoconfigure(bind: array(
    '$config' => "%sonata.seo.config%"
))]
class PageIndexingConfigurator implements PageConfiguratorInterface
{
    const INDEXED = "index, follow";

    const NO_INDEXED = "noindex, nofollow";

    /**
     * Page Configurator Constructor.
     */
    public function __construct(
        private readonly ?SeoPageInterface $seoPage = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handle(PageInterface $page): bool
    {
        return !empty($this->seoPage)
            && ($page instanceof SeoAwarePageInterface)
        ;
    }

    /**
     * @inheritDoc
     */
    public function configure(PageInterface $page, Request $request, array $parameters = array()): bool
    {
        if ($page instanceof SeoAwarePageInterface) {
            $this->configureIndexing($page);
        }

        return true;
    }

    /**
     * Configure Multisite Lang Alternates.
     */
    protected function configureIndexing(SeoAwarePageInterface $page): void
    {
        static $dfIndexing;
        Assert::notEmpty($this->seoPage);
        //==============================================================================
        // Get Default Page Title
        $dfIndexing ??= $this->config["meta"]["name"]["robots"] ?? null;
        //==============================================================================
        // Page Indexing Already Updated (ie. by Controller)
        $current = $this->seoPage->getMetas()["name"]["robots"] ?? null;
        if ($dfIndexing && ($dfIndexing !== $current)) {
            return;
        }
        //==============================================================================
        // Configure Page Indexing
        $this->seoPage->addMeta(
            'name',
            'robots',
            $page->isIndexed() ? static::INDEXED : static::NO_INDEXED
        );
    }
}
