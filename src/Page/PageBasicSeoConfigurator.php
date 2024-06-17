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
use Sonata\PageBundle\Model\PageInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

/**
 * Sonata Pages Basic Seo Configurator
 */
#[AutoconfigureTag(PageConfiguratorInterface::TAG)]
#[Autoconfigure(bind: array(
    '$config' => "%sonata.seo.config%"
))]
class PageBasicSeoConfigurator implements PageConfiguratorInterface
{
    /**
     * Page Configurator Constructor.
     */
    public function __construct(
        private readonly array $config = array(),
        private readonly ?SeoPageInterface $seoPage = null
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
        $this->configureTitle($page);
        $this->configureMetadata($page);

        return true;
    }

    /**
     * Configure the SEO Page Title.
     *
     * @param PageInterface $page
     */
    private function configureTitle(PageInterface $page): void
    {
        static $dfTitle;
        Assert::notEmpty($this->seoPage);
        //==============================================================================
        // Get Default Page Title
        $dfTitle ??= $this->config["title"] ?? null;
        //==============================================================================
        // Page Title Already Updated (ie. by Controller)
        if ($dfTitle && ($dfTitle !== $this->seoPage->getTitle())) {
            return;
        }
        //==============================================================================
        // Inject Page Title
        if ($page->getTitle() || $page->getName()) {
            $title = (string) (empty($page->getTitle()) ? $page->getName() : $page->getTitle());
            $this->seoPage->setTitle($title);
            $this->seoPage->addMeta('property', 'og:title', $title);
        }
    }

    /**
     * Configure the SEO Page Title.
     *
     * @param PageInterface $page
     */
    private function configureMetadata(PageInterface $page): void
    {
        Assert::notEmpty($this->seoPage);
        //==============================================================================
        // Inject Page Description
        if ($page->getMetaDescription()) {
            $this->seoPage->addMeta('name', 'description', (string) $page->getMetaDescription());
        }
        //==============================================================================
        // Inject Page KeyWords
        if ($page->getMetaKeyword()) {
            $this->seoPage->addMeta('name', 'keywords', (string) $page->getMetaKeyword());
        }
    }
}
