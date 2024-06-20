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
 * Sonata Pages Seo Extra Metadata Configurator
 */
#[AutoconfigureTag(PageConfiguratorInterface::TAG)]
#[Autoconfigure(bind: array(
    '$config' => "%sonata.seo.config%"
))]
class PageExtraMetadataConfigurator implements PageConfiguratorInterface
{
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
            $this->configureExtraMetadata($page);
        }

        return true;
    }

    /**
     * Configure Multisite Lang Alternates.
     */
    protected function configureExtraMetadata(SeoAwarePageInterface $page): void
    {
        //====================================================================//
        // Setup Page Extra Metadata
        if (empty($page->getMetaExtra())) {
            return;
        }
        Assert::notEmpty($this->seoPage);
        //====================================================================//
        // Setup Page Extra Metadata
        foreach ($page->getMetaExtra() as $metaExtra) {
            $this->seoPage->addMeta(
                $metaExtra["type"] ?? 'name',
                $metaExtra["name"] ?? '',
                $metaExtra["content"] ?? ''
            );
        }
    }
}
