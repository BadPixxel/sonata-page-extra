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
 * This site implements advanced Seo Rules
 */
interface SeoAwareSiteInterface
{
    //==============================================================================
    // EXTRA ROBOTS
    //==============================================================================

    /**
     * Get Site Robots.txt Extra Contents
     */
    public function getRobotsExtra(): string;

    /**
     * Set Site Robots.txt Extra Contents
     */
    public function setRobotsExtra(?string $contents = null): static;

    //==============================================================================
    // EXTRA METADATA
    //==============================================================================

    /**
     * Get Site Extra Metadata
     */
    public function getMetaExtra(): array;

    /**
     * Set Site Extra Metadata
     */
    public function setMetaExtra(array $metadata): static;
}
