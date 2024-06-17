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

use BadPixxel\SonataPageExtra\Sitemaps\Iterators\PageIterator;
use Iterator;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;

/**
 * Sonata CMS Pages Sitemap Iterator
 */
class CmsPagesIterator extends AbstractSiteIterator
{
    /**
     * @inheritdoc
     */
    public function getSiteIterator(SiteInterface $site): Iterator
    {
        /** @var PageInterface[] $iterable */
        $iterable = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from($this->pageManager->getClass(), 'p')
            ->where('p.routeName = :routeName and p.site = :site')
            ->setParameter('routeName', PageInterface::PAGE_ROUTE_CMS_NAME)
            ->setParameter('site', $site->getId())
            ->getQuery()
            ->execute()
        ;

        return new PageIterator(new \ArrayIterator($iterable));
    }
}
