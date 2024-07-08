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

namespace BadPixxel\SonataPageExtra\Sitemaps;

use BadPixxel\SonataPageExtra\Interfaces\SitemapPageConfiguratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Iterator;
use Sonata\PageBundle\Model\PageManagerInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\SiteManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * Base Sitemap Iterator for Sonata Page Website.
 */
abstract class AbstractSiteIterator extends \AppendIterator implements Iterator
{
    private ?array $sites = null;

    /**
     * @param SitemapPageConfiguratorInterface[] $pageIterators
     */
    public function __construct(
        protected readonly PageManagerInterface $pageManager,
        protected readonly EntityManagerInterface $entityManager,
        #[TaggedIterator(SitemapPageConfiguratorInterface::TAG)]
        protected readonly iterable $pageIterators,
        protected readonly SourceBuilder   $sourceBuilder,
        private readonly SiteManagerInterface $siteManager,
        private readonly RouterInterface      $router,
    ) {
        parent::__construct();
    }

    /**
     * Get Sources Iterator for this Website
     */
    abstract public function getSiteIterator(SiteInterface $site): Iterator;

    /**
     * Get Available Websites for this Host
     *
     * @return SiteInterface[]
     */
    public function getSites(): array
    {
        //====================================================================//
        // Fetch Requested Setup Host
        $host = $this->router->getContext()->getHost();
        //====================================================================//
        // Walk on Sites
        $sites = array();
        foreach ($this->siteManager->findAll() as $site) {
            if ($site->getHost() == $host) {
                $sites[] = $site;
            }
        }

        return $sites;
    }

    /**
     * @throws Exception
     *
     * @return array
     */
    public function current(): array
    {
        Assert::isArray($current = parent::current());

        return $this->sourceBuilder->build($current);
    }

    /**
     * Ensure Init of the Iterator on first Request
     */
    public function valid(): bool
    {
        if (!isset($this->sites)) {
            $this->sites = $this->getSites();
            foreach ($this->sites as $site) {
                $this->append($this->getSiteIterator($site));
            }
        }

        return parent::valid();
    }
}
