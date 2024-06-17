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

use BadPixxel\SonataPageExtra\Sitemaps\Iterators\ConfigurablePageIterator;
use Iterator;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;

/**
 * Sonata Hybrid Pages Sitemap Iterator
 */
class HybridPagesIterator extends AbstractSiteIterator
{
    /**
     * @inheritdoc
     */
    public function getSiteIterator(SiteInterface $site): Iterator
    {
        //==============================================================================
        // Get List of Hybrid Pages
        /** @var PageInterface[] $hybridPages */
        $hybridPages = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from($this->pageManager->getClass(), 'p')
            ->where('p.routeName != :routeName and p.site = :site')
            ->setParameter('routeName', PageInterface::PAGE_ROUTE_CMS_NAME)
            ->setParameter('site', $site->getId())
            ->getQuery()
            ->execute()
        ;

        //==============================================================================
        // Walk on Hybrid Pages to Collect Configurations
        $pagesIterator = new \AppendIterator();
        foreach ($hybridPages as $page) {
            if ($configs = $this->getPageConfigurations($page)) {
                $pagesIterator->append($configs);
            }
        }

        return new ConfigurablePageIterator($pagesIterator);
    }

    public function getPageConfigurations(PageInterface $page): ?Iterator
    {
        $pageConfigurations = array();
        //==============================================================================
        // No Site, No Iterator
        if (!$site = $page->getSite()) {
            return null;
        }
        //==============================================================================
        // Walk on Pages Iterators to get Configurations
        foreach ($this->pageIterators as $pageIterator) {
            if (!$pageIterator->handle($page)) {
                continue;
            }
            foreach ($pageIterator->getConfigurations($page) as $configuration) {
                $pageConfigurations[] = array_merge($configuration, array(
                    "site" => $site,
                    "page" => $page,
                    "route" => $page->getRouteName(),
                ));
            }
        }
        //==============================================================================
        // Add Unique NON Dynamic Pages Configurations
        if (empty($pageConfigurations) && $page->isHybrid() && !$page->isDynamic()) {
            $pageConfigurations[] = array(
                "site" => $site,
                "page" => $page,
                "route" => $page->getRouteName(),
            );
        }
        //==============================================================================
        // No Configuration, No Iterator
        if (empty($pageConfigurations)) {
            return null;
        }

        return new \ArrayIterator($pageConfigurations);
    }
}
