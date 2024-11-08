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

use BadPixxel\SonataPageExtra\Dictionary\RedirectTypes;
use BadPixxel\SonataPageExtra\Entity\ExtendedPage;
use BadPixxel\SonataPageExtra\Entity\PageRedirection;
use BadPixxel\SonataPageExtra\Interfaces\RedirectionAwarePageInterface;
use BadPixxel\SonataPageExtra\Interfaces\SeoAwarePageInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\PageManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/**
 * Apply Configurations to Sonata Page Item
 */
#[Autoconfigure(bind: array(
    '$cmsPageManager' => "@sonata.page.cms.page",
))]
class PageConfigurator
{
    public function __construct(
        private readonly PageManagerInterface $pageManager,
    ) {
    }

    public function configure(PageInterface $page, array $config): void
    {
        //====================================================================//
        // Configure Sonata Page
        $this
            ->setupPageGeneralOptions($page, $config)
            ->setupPageMetadata($page, $config)
            ->setupPageSeoOptions($page, $config)
            ->setupPageRedirections($page, $config)
        ;
        //====================================================================//
        // Save Sonata Page
        $this->pageManager->save($page);
    }

    /**
     * Set up a Page General Options.
     */
    private function setupPageGeneralOptions(PageInterface $page, array $config): static
    {
        //====================================================================//
        // Update Enabled Flag
        if (is_bool($config['enabled'])) {
            $page->setEnabled($config['enabled']);
        }
        //====================================================================//
        // Update Parent
        if ($config['parent'] instanceof PageInterface) {
            $page->setParent($config['parent']);
        }
        //====================================================================//
        // Update Generic Text Values
        $keys = array('name', 'type', 'templateCode', 'pageAlias');
        foreach ($keys as $key) {
            if (!empty($config[$key])) {
                $page->{ 'set'.ucfirst($key) }((string) $config[$key]);
            }
        }

        return $this;
    }

    /**
     * Set up a Page Metadata.
     */
    private function setupPageMetadata(PageInterface $page, array $config): static
    {
        //====================================================================//
        // Update Generic Text Values
        $keys = array('slug', 'customUrl', 'title', 'metaKeyword', 'metaDescription');
        foreach ($keys as $key) {
            if (!empty($config[$key])) {
                $page->{ 'set'.ucfirst($key) }((string) $config[$key]);
            }
        }
        //====================================================================//
        // Update Position
        if (!empty($config['position'])) {
            $page->setPosition((int) $config['position']);
        }

        return $this;
    }

    /**
     * Set up a Page General Options.
     */
    private function setupPageSeoOptions(PageInterface $page, array $config): static
    {
        if (!$page instanceof SeoAwarePageInterface) {
            return $this;
        }
        //====================================================================//
        // Update Indexed Flag
        if (is_bool($config['indexed'])) {
            $page->setIndexed($config['indexed']);
        }
        //====================================================================//
        // Update Meta Extras
        if (is_array($config['metaExtras'])) {
            foreach ($config['metaExtras'] as $metadata) {
                Assert::keyExists($metadata, 'type');
                Assert::keyExists($metadata, 'name');
                Assert::keyExists($metadata, 'content');
            }

            $page->setMetaExtra($config['metaExtras']);
        }

        return $this;
    }

    /**
     * Set up a Page General Options.
     */
    private function setupPageRedirections(PageInterface $page, array $config): static
    {
        if (!$page instanceof RedirectionAwarePageInterface) {
            return $this;
        }
        Assert::isInstanceOf($page, ExtendedPage::class);
        //====================================================================//
        // Get Configured Redirections
        $redirections = $config['redirections'] ?? null;
        if (!is_array($config['redirections'])) {
            return $this;
        }
        //====================================================================//
        // Walk on Redirections
        $doneRedirections = array();
        foreach ($redirections as $uri => $code) {
            //====================================================================//
            // Safety Check
            Assert::stringNotEmpty($uri, "Redirect url is invalid");
            Assert::inArray(
                $code,
                RedirectTypes::ALL,
                sprintf('%s Redirection code not allowed', $code)
            );
            //====================================================================//
            // Ensure Redirection Exists
            if (!$redirection = $page->getRedirectionByUri($uri)) {
                $redirection = new PageRedirection();
                $redirection->setPage($page);
                $page->addRedirection($redirection);
            }
            //====================================================================//
            // Update Redirection Uri
            $redirection->setUri($uri);
            $doneRedirections[] = $uri;
            //====================================================================//
            // Update Redirection Code
            if (in_array($code, RedirectTypes::ALL, true)) {
                $redirection->setCode($code);
            } else {
                $redirection->setCode(Response::HTTP_MOVED_PERMANENTLY);
            }
        }
        //====================================================================//
        // Delete Extra Redirection
        foreach ($page->getRedirections() as $redirection) {
            if (!in_array($redirection->getUri(), $doneRedirections, true)) {
                $page->removeRedirection($redirection);
            }
        }

        return $this;
    }
}
