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

namespace BadPixxel\SonataPageExtra\Helpers;

use BadPixxel\SonataPageExtra\Actions\WebsiteRedirectAction;
use BadPixxel\SonataPageExtra\Entity\PageRedirection;
use Sonata\PageBundle\Model\PageInterface;
use Symfony\Component\Routing\Route;
use Webmozart\Assert\Assert;

/**
 * Transform a Page Redirection to a Symfony Route
 */
class RedirectRouteBuilder
{
    public static function fromPageRedirection(PageRedirection $redirection): Route
    {
        //==============================================================================
        // Fetch Target Page
        $page = $redirection->getPage();
        //==============================================================================
        // Build CMS Page Redirection
        if ($page->isCms()) {
            return self::isPageAlias($page->getPageAlias())
                ? self::getRouteFromPageAlias($redirection, $page)
                : self::getRouteFromPagePath($redirection, $page)
            ;
        }

        //==============================================================================
        // Build Route Redirection
        return self::getRouteFromPageRoute($redirection, $page);
    }

    /**
     * Build Redirect Route from Page Alias
     */
    public static function getRouteFromPageAlias(PageRedirection $redirection, PageInterface $page): Route
    {
        return self::toRoute($redirection, array(
            "route" => $page->getPageAlias()
        ));
    }

    /**
     * Build Redirect Route from Page Path
     */
    public static function getRouteFromPagePath(PageRedirection $redirection, PageInterface $page): Route
    {
        return self::toRoute($redirection, array(
            "path" => $page->getCustomUrl() ?? $page->getUrl(),
        ));
    }

    /**
     * Build Redirect Route from Page Route
     */
    public static function getRouteFromPageRoute(PageRedirection $redirection, PageInterface $page): Route
    {
        return self::toRoute($redirection, array(
            "route" => $page->getRouteName()
        ));
    }

    /**
     * Build Generic Route Parameters
     */
    private static function toRoute(PageRedirection $redirection, array $parameters): Route
    {
        $site = $redirection->getPage()->getSite();
        Assert::notEmpty($site);

        return new Route($redirection->getUri(), array_replace_recursive(array(
            '_controller' => WebsiteRedirectAction::class,
            'permanent' => $redirection->isPermanent(),
            'site' => $site->getId(),
        ), $parameters));
    }

    private static function isPageAlias(?string $name): bool
    {
        return $name && str_starts_with($name, '_page_alias_');
    }
}
