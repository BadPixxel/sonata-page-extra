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

namespace BadPixxel\SonataPageExtra\DependencyInjection;

use BadPixxel\SonataPageExtra\Entity;
use BadPixxel\SonataPageExtra\Page\BadPixxelPageService;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Webmozart\Assert\Assert;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BadPixxelSonataPageExtrasExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('admin.yaml');

        //==============================================================================
        // Load List of Sonata Page Configuration Files for All Bundles
        $this->loadBundlesPageConfigurations($config, $container);
    }

    /**
     * @inheritDoc
     */
    public function prepend(ContainerBuilder $container): void
    {
        //==============================================================================
        // Autoconfigure Sonata Page Bundle
        $container->prependExtensionConfig('sonata_page', array(
            'class' => array(
                'page' => Entity\ExtendedPage::class,
                'site' => Entity\ExtendedSite::class,
                'block' => Entity\Block::class,
                'snapshot' => Entity\Snapshot::class,
            ),
            'default_page_service' => BadPixxelPageService::class,
            'ignore_route_patterns' => array(
                '^(.*)Redirect(.*)'
            ),
            'ignore_uri_patterns' => array(
                '^/robots.txt',
                '^/sitemap.xml',
            ),
            'ignore_routes' => array(
                'badpixxel_sonata_extras_sitemap',
                'badpixxel_sonata_extras_robots',
            ),
        ));
    }

    /**
     * Load List of Sonata Page Configuration Files for All Bundles
     */
    private function loadBundlesPageConfigurations(array $config, ContainerBuilder $container): void
    {
        Assert::isArray($configFiles = $config['page']['configurations']);
        /** @var array<string, class-string> $bundles */
        $bundles = $container->getParameter('kernel.bundles');
        foreach ($bundles as $bundle) {
            $configFiles = array_merge($configFiles, $this->loadBundlePageConfigurations($bundle));
        }

        $container->setParameter('badpixxel_sonata_page_extra.pages.configurations', $configFiles);
    }

    /**
     * Load List of Sonata Page Configuration Files
     */
    private function loadBundlePageConfigurations(string $bundle): array
    {
        $files = array();
        Assert::isAOf($bundle, BundleInterface::class);

        //==============================================================================
        // Detect Bundle Configuration Path
        try {
            $reflected = new \ReflectionClass($bundle);
            $bundlePath = sprintf("%s/Resources/config/pages", \dirname((string) $reflected->getFileName()));
        } catch (\ReflectionException $e) {
            return $files;
        }
        //==============================================================================
        // Ensure Directory
        if (!is_dir($bundlePath)) {
            return $files;
        }
        //==============================================================================
        // Search for Configuration Files
        $finder = new Finder();
        $finder->files()
            ->in($bundlePath)
            ->name(array("*.yml", "*.yaml"))
        ;
        foreach ($finder as $file) {
            if ($realpath = $file->getRealPath()) {
                $files[] = $realpath;
            }
        }

        return $files;
    }
}
