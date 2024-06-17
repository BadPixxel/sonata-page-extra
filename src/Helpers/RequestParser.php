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

use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

/**
 * Extract Information from Current Request
 */
class RequestParser
{
    /**
     * Extract Route Name from Symfony Request
     */
    public static function getRoute(Request $request): string
    {
        Assert::string($route = $request->attributes->get("_route"));

        return $route;
    }

    /**
     *  Extract Route Parameters from Symfony Request
     */
    public static function getRouteParams(Request $request): array
    {
        Assert::isArray($params = $request->attributes->get("_route_params"));
        if (array_key_exists("page", $params)) {
            unset($params["page"]);
        }
        if (array_key_exists("params", $params)) {
            unset($params["params"]);
        }

        return $params;
    }

    /**
     *  Extract Route Parameters from Symfony Request
     */
    public static function getRouteParamsNoPath(Request $request): array
    {
        $params = self::getRouteParams($request);
        if (array_key_exists("path", $params)) {
            unset($params["path"]);
        }

        return $params;
    }
}
