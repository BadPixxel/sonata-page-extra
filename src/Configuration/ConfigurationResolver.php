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

namespace BadPixxel\SonataPageExtra\Configuration;

use Exception;
use Sonata\PageBundle\Model\SiteInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

/**
 * Resolve & Validate Page Configuration
 */
class ConfigurationResolver
{
    const AUTOLOAD = array(
        "position",
        "name",
        "type",
        "templateCode",
        "pageAlias",

        "slug",
        "customUrl",
        "title",
        "metaKeyword",
        "metaDescription",
    );

    /**
     * Sonata Page Options Resolver
     */
    private OptionsResolver $resolver;

    public function __construct(
        private readonly PageIdentifier $identifier,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Resolve Page Configuration for a Website.
     *
     * @throws Exception
     */
    public function resolve(SiteInterface $site, array $configuration): ?array
    {
        //==============================================================================
        // Ensure resolver Init
        $this->resolver ??= $this->getOptionResolver();
        //==============================================================================
        // Resolve Main Configuration
        $mainConfig = $this->resolver->resolve($configuration);
        //==============================================================================
        // Autoload config from Translations
        if ($mainConfig["autoload"]) {
            $mainConfig = $this->autoloadFromTranslations($site, $mainConfig);
        }
        //==============================================================================
        // Resolve Configuration for Website
        if ($siteConfiguration = $configuration['sites'][$site->getName()] ?? null) {
            //==============================================================================
            // Resolve Main Configuration
            $siteConfig = $this->resolver->resolve($siteConfiguration);
            //==============================================================================
            // Autoload config from Translations
            if ($siteConfig["autoload"]) {
                $siteConfig = $this->autoloadFromTranslations($site, $siteConfig);
            }
        }
        //==============================================================================
        // Build Configuration for Website
        $finalConfig = array_replace_recursive(
            $mainConfig,
            array_filter($siteConfig ?? array())
        );
        //==============================================================================
        // Page is Excluded
        if ($finalConfig['excluded'] ?? false) {
            return null;
        }
        //==============================================================================
        // Resolve Page Parent
        if ($finalConfig['parent']) {
            $finalConfig['parent'] = $this->identifier->identify($site, $finalConfig['parent']);
        }

        return $finalConfig;
    }

    //==============================================================================
    // PRIVATE METHODS
    //==============================================================================

    public function autoloadFromTranslations(SiteInterface $site, array $config): array
    {
        Assert::string($config["autoload"]);
        Assert::string($domain = $config["translation_domain"]);
        Assert::notEmpty($locale = $site->getLocale());
        //====================================================================//
        // Get Translator Catalogue
        Assert::notEmpty($catalogue = $this->getTranslatorCatalogue($locale));
        //====================================================================//
        // Get Translator Fallback Catalogue
        $fallBackCatalogue = $catalogue->getFallbackCatalogue();
        //====================================================================//
        // Walk on Auto-detected Scalar Properties
        foreach (self::AUTOLOAD as $optionName) {
            //====================================================================//
            // Check if Translation is Defined
            $propertyKey = sprintf("%s.%s", $config["autoload"], $optionName);
            if (!$catalogue->defines($propertyKey, $domain)) {
                if (!$fallBackCatalogue || !$fallBackCatalogue->defines($propertyKey, $domain)) {
                    continue;
                }
            }
            //====================================================================//
            // Setup Value from Translation
            $config[$optionName] ??= $this->translator->trans($propertyKey, array(), $domain, $locale);
        }

        return $config;
    }

    /**
     * Get Current Translator Catalogue for Locale
     */
    private function getTranslatorCatalogue(string $locale): ?MessageCatalogueInterface
    {
        //====================================================================//
        // Ensure Translator is Compatible
        if (!$this->translator instanceof TranslatorBagInterface) {
            return null;
        }

        return $this->translator->getCatalogue($locale);
    }

    /**
     * Build Page Resolver
     */
    private function getOptionResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults(array(
                "autoload" => null,
                "excluded" => false,

                "enabled" => null,
                "name" => null,
                "parent" => null,
                "type" => null,
                "templateCode" => null,
                "pageAlias" => null,
                "position" => null,

                "slug" => null,
                "customUrl" => null,
                "title" => null,
                "metaKeyword" => null,
                "metaDescription" => null,
                "metaExtras" => null,

                "indexed" => null,
                "redirections" => null,

                "translation_domain" => "messages",
                "sites" => array(),
            ))
            ->addAllowedTypes("autoload", array('null', 'string'))
            ->addAllowedTypes("excluded", 'bool')

            ->addAllowedTypes("enabled", array('null', 'boolean'))
            ->addAllowedTypes("name", array('null', 'string'))
            ->addAllowedTypes("type", array('null', 'string'))
            ->addAllowedTypes("templateCode", array('null', 'string'))
            ->addAllowedTypes("pageAlias", array('null', 'string'))
            ->addAllowedTypes("parent", array('null', 'string'))
            ->addAllowedTypes("position", array('null', 'int', 'string'))

            ->addAllowedTypes("slug", array('null', 'string'))
            ->addAllowedTypes("customUrl", array('null', 'string'))
            ->addAllowedTypes("title", array('null', 'string'))
            ->addAllowedTypes("metaKeyword", array('null', 'string'))
            ->addAllowedTypes("metaDescription", array('null', 'string'))
            ->addAllowedTypes("metaExtras", array('null', "array"))

            ->addAllowedTypes("indexed", array('null', 'boolean'))
            ->addAllowedTypes("redirections", array('null', "array"))

            ->addAllowedTypes("translation_domain", 'string')
            ->addAllowedTypes("sites", "array")
        ;

        return $resolver;
    }
}
