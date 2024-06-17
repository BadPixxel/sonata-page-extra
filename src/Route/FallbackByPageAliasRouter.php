<?php

declare(strict_types=1);

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

namespace BadPixxel\SonataPageExtra\Route;

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Websites Fallbacks on Default Website by Pages Alias
 */
final class FallbackByPageAliasRouter extends AbstractExtraPageRouter
{
    /**
     * @inheritdoc
     */
    public function generate(
        string $name,
        array $parameters = array(),
        int $referenceType = self::ABSOLUTE_PATH
    ): string {
        //==============================================================================
        // Check if we are on Default Website
        $dfSite = $this->hostsManager->getDefaultSite();
        if ($dfSite === $this->hostsManager->getCurrentSite()) {
            throw new RouteNotFoundException();
        }
        //==============================================================================
        // Check if Page is Found on Default Website
        $page = $this->cmsManager->getPageByPageAlias($dfSite, $name);

        return $this->cmsRouter->generate($page, $parameters, $referenceType);
    }
}
