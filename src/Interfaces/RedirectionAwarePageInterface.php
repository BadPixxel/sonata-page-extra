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

use BadPixxel\SonataPageExtra\Entity\PageRedirection;
use Doctrine\Common\Collections\Collection;

/**
 * This page implements Custom Urls Redirections
 */
interface RedirectionAwarePageInterface
{
    //==============================================================================
    // PAGE REDIRECTIONS LIST
    //==============================================================================

    /**
     * Initialize Page Redirections Collection
     */
    public function initRedirections(): static;

    /**
     * Add a Redirection to Page Redirections Collection
     */
    public function addRedirection(PageRedirection $redirection): static;

    /**
     * Remove a Redirection to Page Redirections Collection
     */
    public function removeRedirection(PageRedirection $redirection): static;

    /**
     * Get Page Redirections Collection
     *
     * @return Collection<PageRedirection>
     */
    public function getRedirections(): Collection;
}
