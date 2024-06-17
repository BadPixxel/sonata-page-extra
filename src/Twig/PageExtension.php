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

namespace BadPixxel\SonataPageExtra\Twig;

use BadPixxel\SonataPageExtra\Services\LocalesManager;
use BadPixxel\SonataPageExtra\Services\WebsiteManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PageExtension extends AbstractExtension
{
    public function __construct(
        private readonly WebsiteManager $hostsManager,
        private readonly LocalesManager $localesManager,
    ) {
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('site_url', array($this->hostsManager, 'generateSiteUrl')),
            new TwigFunction('site_path', array($this->hostsManager, 'generateSitePath')),
            new TwigFunction('locales_alternates', array($this->localesManager, 'getLocaleAlternates')),
        );
    }
}
