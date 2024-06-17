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

use Sonata\PageBundle\Model\PageInterface;
use Webmozart\Assert\Assert;

/**
 * Configurable Page Iterator
 */
class ConfigurablePageIterator extends \FilterIterator
{
    /**
     * Filter Pages
     */
    public function accept(): bool
    {
        $value = $this->getInnerIterator()->current();
        Assert::isArray($value);
        Assert::isInstanceOf($value['page'] ?? null, PageInterface::class);
        //==============================================================================
        // Filter Non Indexed Pages
        if (!PageIterator::isIndexed($value['page'])) {
            return false;
        }

        return true;
    }
}
