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
use BadPixxel\SonataPageExtra\Interfaces\SeoAwareSiteInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

/**
 * Sonata Pages from Site Seo Extra Metadata Configurator
 */
#[AutoconfigureTag(PageConfiguratorInterface::TAG)]
#[Autoconfigure(bind: array(
    '$config' => "%sonata.seo.config%"
))]
class SiteExtraMetadataConfigurator implements PageConfiguratorInterface
{
    /**
     * Page Configurator Constructor.
     */
    public function __construct(
        private readonly array $config = array(),
        private readonly ?SeoPageInterface $seoPage = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handle(PageInterface $page): bool
    {
        return !empty($this->seoPage)
            && ($page->getSite() instanceof SeoAwareSiteInterface)
        ;
    }

    /**
     * @inheritDoc
     */
    public function configure(PageInterface $page, Request $request, array $parameters = array()): bool
    {
        if ($page->getSite() instanceof SeoAwareSiteInterface) {
            $this->configureExtraMetadata($page);
        }

        return true;
    }

    /**
     * Configure Multisite Lang Alternates.
     */
    protected function configureExtraMetadata(PageInterface $page): void
    {
        //====================================================================//
        // Page has Site
        if (!$site = $page->getSite()) {
            return;
        }
        Assert::isInstanceOf($site, SeoAwareSiteInterface::class);
        //====================================================================//
        // Setup Page Extra Metadata
        if (empty($site->getMetaExtra())) {
            return;
        }
        Assert::notEmpty($this->seoPage);
        //====================================================================//
        // Setup Page Extra Metadata
        foreach ($site->getMetaExtra() as $metaExtra) {
            //====================================================================//
            // Metadata Already defined and different from default value
            if ($this->isMetaAlreadyConfigured($metaExtra["type"] ?? 'name', $metaExtra["name"])) {
                continue;
            }
            //====================================================================//
            // Configure Metadata
            $this->seoPage->addMeta(
                $metaExtra["type"] ?? 'name',
                $metaExtra["name"] ?? '',
                $metaExtra["content"] ?? ''
            );
        }
    }

    /**
     * Check if a Page Metadata is Already defined and different from default value.
     */
    private function isMetaAlreadyConfigured(string $type, string $name): bool
    {
        Assert::notEmpty($this->seoPage);
        //====================================================================//
        // Metadata Not Defined in Seo Page
        if (empty($seoMetadata = $this->seoPage->getMetas()[$type][$name] ?? null)) {
            return false;
        }
        //====================================================================//
        // Metadata Already Updated by Controller
        if (!empty($configMetadata = $this->config[$type][$name] ?? null) && ($seoMetadata != $configMetadata)) {
            return true;
        }

        return false;
    }
}
