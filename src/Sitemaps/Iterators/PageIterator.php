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

namespace BadPixxel\SonataPageExtra\Sitemaps\Iterators;

use BadPixxel\SonataPageExtra\Interfaces\SeoAwarePageInterface;
use FilterIterator;
use Sonata\PageBundle\Model\PageInterface;
use Webmozart\Assert\Assert;

class PageIterator extends FilterIterator
{
    /**
     * Convert Page to Sitemap Parameters
     */
    public function current(): array
    {
        $page = parent::current();
        \assert($page instanceof PageInterface);

        return array(
            'site' => $page->getSite(),
            'route' => $page->getRouteName(),
            'route_parameters' => array("path" => $page->getUrl()),
            'lastmod' => $page->getUpdatedAt()?->format('Y-m-d'),
        );
    }

    /**
     * Filter Pages
     */
    public function accept(): bool
    {
        $page = $this->getInnerIterator()->current();
        Assert::isInstanceOf($page, PageInterface::class);

        return self::isIndexed($page);
    }

    /**
     * Filter Pages
     */
    public static function isIndexed(PageInterface $page): bool
    {
        //==============================================================================
        // Filter Non Indexed Pages
        if (!$page->getEnabled()) {
            return false;
        }
        //==============================================================================
        // Filter Non Indexed Pages
        if ($page instanceof SeoAwarePageInterface) {
            if (!$page->isIndexed()) {
                return false;
            }
        }

        return true;
    }
}
