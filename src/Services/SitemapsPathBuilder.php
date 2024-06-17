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

namespace BadPixxel\SonataPageExtra\Services;

use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

/**
 * Build paths for Host Sitemaps
 */
class SitemapsPathBuilder
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private string $publicDir = "public"
    ) {
    }

    /**
     * Generate final sitemaps path for host
     */
    public function ensureHostPathExists(string $host): void
    {
        $mainDir = $this->getAbsolutePath();
        Assert::directory($mainDir, sprintf("Sitemaps: %s must be a directory", $mainDir));
        $hostDir = $this->getAbsolutePath($host);
        Assert::directory($hostDir, sprintf("Sitemaps: %s must be a directory", $hostDir));
    }

    /**
     * Generate Relative sitemaps path for host
     */
    public function getRelativePath(?string $host = null): string
    {
        $dir = $this->publicDir.'/maps';
        $dir .= $host ? "/".$host : null;

        return $dir;
    }

    /**
     * Generate Relative sitemaps path for host sitemap file
     */
    public function getFinalRelativePath(string $host = null): string
    {
        return $this->getRelativePath($host)."/sitemap.xml";
    }

    /**
     * Generate Absolute sitemaps path for host sitemap file
     */
    public function getFinalAbsolutePath(string $host = null): string
    {
        return $this->getAbsolutePath($host)."/sitemap.xml";
    }

    /**
     * Generate Absolute sitemaps path for host
     */
    private function getAbsolutePath(?string $host = null): string
    {
        return $this->kernel->getProjectDir()."/".$this->getRelativePath($host);
    }
}
