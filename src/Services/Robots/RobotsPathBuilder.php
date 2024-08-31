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

use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

/**
 * Build paths for Host Robot.txt
 */
class RobotsPathBuilder
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private string $publicDir = "public"
    ) {
    }

    /**
     * Ensure robots.txt public path exists
     */
    public function ensurePublicPathExists(): void
    {
        $mainDir = $this->getAbsolutePath();
        Assert::directory($mainDir, sprintf("Robots: %s must be a directory", $mainDir));
    }

    /**
     * Generate Relative sitemaps path for host
     */
    public function getRelativePath(?string $host = null): string
    {
        $dir = $this->publicDir.'/robots';
        $dir .= $host ? "/".$host : null;

        return $dir;
    }

    /**
     * Generate Relative robots.txt path for host sitemap file
     */
    public function getFinalRelativePath(string $host = null): string
    {
        return $this->getRelativePath($host)."/robots.txt";
    }

    /**
     * Generate Absolute robots.txt path for host sitemap file
     */
    public function getFinalAbsolutePath(string $host = null): string
    {
        return $this->getAbsolutePath($host)."/robots.txt";
    }

    /**
     * Generate Absolute robots.txt path for host
     */
    private function getAbsolutePath(?string $host = null): string
    {
        return $this->kernel->getProjectDir()."/".$this->getRelativePath($host);
    }
}
