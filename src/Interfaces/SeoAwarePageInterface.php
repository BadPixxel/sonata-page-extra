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

namespace BadPixxel\SonataPageExtra\Interfaces;

/**
 * This page implements advanced Seo Rules
 */
interface SeoAwarePageInterface
{
    //==============================================================================
    // PAGE INDEXING OPTIONS
    //==============================================================================

    /**
     * Check if Page is Indexed
     */
    public function isIndexed(): bool;

    /**
     * Set Page as Indexed
     */
    public function setIndexed(bool $indexed): static;

    //==============================================================================
    // EXTRA METADATA
    //==============================================================================

    /**
     * Get Page Extra Metadata
     */
    public function getMetaExtra(): array;

    /**
     * Set Page Extra Metadata
     */
    public function setMetaExtra(array $metadata): static;
}
