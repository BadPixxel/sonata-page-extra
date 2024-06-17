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
use BadPixxel\SonataPageExtra\Services\LocalesManager;
use BadPixxel\SonataPageExtra\Services\WebsiteManager;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Sonata Pages Basic Seo Configurator
 */
#[AutoconfigureTag(PageConfiguratorInterface::TAG)]
#[Autoconfigure(bind: array(
    '$config' => "%sonata.seo.config%"
))]
class PageLangAlternatesConfigurator implements PageConfiguratorInterface
{
    /**
     * Page Configurator Constructor.
     */
    public function __construct(
        private readonly WebsiteManager    $hostsManager,
        private readonly LocalesManager    $localesManager,
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
        //==============================================================================
        // Current Multisite Strategy Handle Multi-lang
        if ($this->hostsManager->isMultiLang()) {
            $this->configureLangAlternates($page, $request);
        }

        return true;
    }

    /**
     * Configure Multisite Lang Alternates.
     */
    protected function configureLangAlternates(PageInterface $page, Request $request): void
    {
        //==============================================================================
        // Lang Alternates Already Configured
        if (!$this->seoPage || !empty($this->seoPage->getLangAlternates())) {
            return;
        }
        //==============================================================================
        // Add Lang Alternates for This Page
        $this->seoPage->setLangAlternates($this->localesManager->getLangAlternates($page, $request));
    }
}
