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

namespace BadPixxel\SonataPageExtra\Services\Robots;

use BadPixxel\SonataPageExtra\Interfaces\SeoAwareSiteInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * Build Contents for Host Robot.txt
 */
class RobotsContentsBuilder
{
    public function __construct(
        private readonly RobotsPathBuilder $pathBuilder,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * Generate final robots.txt path for host
     */
    public function build(SiteInterface $site): string
    {
        $robotsTxt = "";
        //==============================================================================
        // Safety Checks
        $this->pathBuilder->ensurePublicPathExists();
        //==============================================================================
        // Load Base Robots.txt from Default Static Sources
        $robotsTxt .= $this->getDefaultContents();
        //==============================================================================
        // Load Base Robots.txt from Host Static Sources
        $robotsTxt .= $this->getHostContents($site->getHost());
        //==============================================================================
        // Load Extra Robots.txt from Site Entity
        $robotsTxt .= $this->getSiteContents($site);

        //==============================================================================
        // Ensure Sitemap Url is Defined
        return $this->ensureSitemapUrl($robotsTxt);
    }

    /**
     * Load contents for robots.txt from Default Static Sources
     */
    private function getDefaultContents(): string
    {
        //==============================================================================
        // Build Default Static Sources Path
        $path = $this->pathBuilder->getFinalAbsolutePath();
        //==============================================================================
        // Build Contents
        $robotsTxt = $this->getBlock("Default Contents", $path);
        //==============================================================================
        // Load Base Robots.txt from Default Static Sources
        if (is_file($path)) {
            Assert::readable($path);
            $robotsTxt .= file_get_contents($path);
        }
        $robotsTxt .= PHP_EOL.PHP_EOL;

        return $robotsTxt;
    }

    /**
     * Load contents for robots.txt from Host Static Sources
     */
    private function getHostContents(string $host = null): string
    {
        //==============================================================================
        // Build Default Static Sources Path
        $path = $this->pathBuilder->getFinalAbsolutePath($host);
        //==============================================================================
        // Build Contents
        $robotsTxt = $this->getBlock("Host Contents", $this->pathBuilder->getRelativePath($host));
        //==============================================================================
        // Load Base Robots.txt from Host Static Sources
        if (is_file($path)) {
            Assert::readable($path);
            $robotsTxt .= file_get_contents($path);
        }
        $robotsTxt .= PHP_EOL.PHP_EOL;

        return $robotsTxt;
    }

    /**
     * Load contents for robots.txt from Site Entity
     */
    private function getSiteContents(SiteInterface $site): string
    {
        if (!$site instanceof SeoAwareSiteInterface) {
            return "";
        }
        //==============================================================================
        // Build Contents
        $robotsTxt = $this->getBlock("Site Extra Contents", (string) $site->getName());
        //==============================================================================
        // Load Base Robots.txt from Host Static Sources
        if (!empty($contents = $site->getRobotsExtra())) {
            $robotsTxt .= $contents;
        }
        $robotsTxt .= PHP_EOL.PHP_EOL;

        return $robotsTxt;
    }

    /**
     * Load contents for robots.txt from Site Entity
     */
    private function ensureSitemapUrl(string $robotsTxt): string
    {
        //==============================================================================
        // Explode Lines to array
        $array = preg_split("/(\r\n|\n|\r)/", $robotsTxt);
        //==============================================================================
        // Walk on Lines to Find Sitemap Url
        foreach ($array ?: array() as $line) {
            if (str_starts_with($line, "sitemap:")) {
                return $robotsTxt;
            }
        }
        //==============================================================================
        // Build Contents
        $robotsTxt .= $this->getBlock("Sitemap Url", "Symfony Router");
        $robotsTxt .= sprintf("sitemap: %s", $this->router->generate(
            "badpixxel_sonata_extras_sitemap",
            array(),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        $robotsTxt .= PHP_EOL.PHP_EOL;

        return $robotsTxt;
    }

    /**
     * Load contents for robots.txt from Host Static Sources
     */
    private function getBlock(string $title, string $path): string
    {
        $split = "###----------------------------------------------------------";

        $robotsTxt = $split.PHP_EOL;
        $robotsTxt .= sprintf("# %s from %s", $title, $path).PHP_EOL;
        $robotsTxt .= $split.PHP_EOL.PHP_EOL;

        return $robotsTxt;
    }
}
