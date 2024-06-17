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

namespace BadPixxel\SonataPageExtra\Sitemaps;

use BadPixxel\SonataPageExtra\Dictionary\SitemapFrequency;
use BadPixxel\SonataPageExtra\Services\WebsiteManager;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * Build Sitemap Source from Received Information
 */
class SourceBuilder
{
    public function __construct(
        private readonly WebsiteManager  $hostsManager,
        private readonly RouterInterface $router
    ) {
    }

    /**
     * Transform Source Information to Sitemap Url.
     *
     * @throws \Exception
     */
    public function build(array $source): array
    {
        //====================================================================//
        // Resolve Source Information
        $resSource = $this->resolve($source);
        //====================================================================//
        // Compute Page Absolute Url for Sonata Site
        if ($site = $resSource['site'] ?? null) {
            Assert::isInstanceOf($site, SiteInterface::class);
            $targetUrl = $this->hostsManager->generateSiteUrl(
                $site,
                $resSource["route"],
                $resSource["route_parameters"]
            );
        } else {
            $targetUrl = $this->router->generate(
                $resSource["route"],
                $resSource["route_parameters"],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }
        //====================================================================//
        // Filter Empty Values
        $closure = function ($value) {
            return !empty($value);
        };

        //====================================================================//
        // Return Site Map Contents
        return array_filter(array(
            'url' => $targetUrl,
            'changefreq' => $resSource["changefreq"],
            'priority' => $resSource["priority"],
            'lastmod' => $resSource["lastmod"],
        ), $closure);
    }

    //====================================================================//
    // Url Informations Validation
    //====================================================================//

    /**
     * Resolve Sitemap Url Options
     *
     * @throws \Exception
     */
    private function resolve(array $options): array
    {
        /** @var null|OptionsResolver $resolver */
        static $resolver;

        //====================================================================//
        // Setup Resolver if Needed
        if (!isset($resolver)) {
            $resolver = new OptionsResolver();
            $this->configureOptions($resolver);
        }

        return $resolver->resolve($options);
    }

    /**
     * Configure Options for Sitemaps Url Resolver
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'site' => null,
            'page' => null,
            'route' => null,
            'route_parameters' => array(),
            'changefreq' => SitemapFrequency::WEEKLY,
            'priority' => 0.5,
            'lastmod' => null,
        ));

        $resolver->setAllowedTypes('site', array('null', SiteInterface::class));
        $resolver->setAllowedTypes('page', array('null', PageInterface::class));
        $resolver->setAllowedTypes('route', 'string');
        $resolver->setAllowedTypes('route_parameters', 'array');
        $resolver->setAllowedTypes('changefreq', 'string');
        $resolver->setAllowedValues('changefreq', SitemapFrequency::getAll());
        $resolver->setAllowedTypes('priority', array('int', 'double', 'string'));
        $resolver->setAllowedTypes('lastmod', array('null', 'string'));
    }
}
