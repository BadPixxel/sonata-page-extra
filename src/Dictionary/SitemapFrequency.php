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

namespace BadPixxel\SonataPageExtra\Dictionary;

/**
 * Class defining possible sitemap frequencies.
 */
class SitemapFrequency
{
    /**
     * Indicates that the sitemap should be updated as often as possible.
     */
    const ALWAYS = 'always';

    /**
     * Indicates that the sitemap should be updated every hour.
     */
    const HOURLY = 'hourly';

    /**
     * Indicates that the sitemap should be updated every day.
     */
    const DAILY = 'daily';

    /**
     * Indicates that the sitemap should be updated every week.
     */
    const WEEKLY = 'weekly';

    /**
     * Indicates that the sitemap should be updated every month.
     */
    const MONTHLY = 'monthly';

    /**
     * Indicates that the sitemap should be updated every year.
     */
    const YEARLY = 'yearly';

    /**
     * Indicates that the sitemap should never be updated.
     */
    const NEVER = 'never';

    /**
     * Returns all possible sitemap frequency values.
     *
     * @return array Array containing all frequency values.
     */
    public static function getAll(): array
    {
        return array(
            self::ALWAYS,
            self::HOURLY,
            self::DAILY,
            self::WEEKLY,
            self::MONTHLY,
            self::YEARLY,
            self::NEVER,
        );
    }
}
