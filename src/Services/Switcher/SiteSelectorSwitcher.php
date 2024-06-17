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

namespace BadPixxel\SonataPageExtra\Services\Switcher;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Site\SiteSelectorInterface;
use Webmozart\Assert\Assert;

/**
 * Switch Site selector current site to Different Site/Hosts Contexts
 */

class SiteSelectorSwitcher
{
    /**
     * Currently Used Website
     */
    private ?SiteInterface $currentSite = null;

    /**
     * Storage for Site Reflexion Property
     */
    private ?ReflectionProperty $property = null;

    public function __construct(
        private SiteSelectorInterface $siteSelector,
    ) {
    }

    /**
     * Change Current Website & Return Default
     */
    public function switchTo(SiteInterface $site): ?SiteInterface
    {
        Assert::notEmpty($property = $this->getProperty());
        //====================================================================//
        // Store Current Site Selector Website
        $currentSite = $property->getValue($this->siteSelector);
        $this->currentSite = ($currentSite instanceof SiteInterface) ? $currentSite : null;
        //====================================================================//
        // Replace Site Selector Website
        $property->setValue($this->siteSelector, $site);

        return $this->currentSite;
    }

    /**
     * Restore Current Website Context
     */
    public function reset(): void
    {
        Assert::notEmpty($property = $this->getProperty());
        //====================================================================//
        // Reset Site Selector Website
        $property->setValue($this->siteSelector, $this->currentSite);
        $this->currentSite = null;
    }

    /**
     * Get Site Selector Site Reflexion Property
     */
    public function getProperty(): ?ReflectionProperty
    {
        if (!isset($this->property)) {
            try {
                $reflexionClass = new ReflectionClass($this->siteSelector);
                $this->property = $reflexionClass->getProperty("site");
                $this->property->setAccessible(true);
            } catch (ReflectionException $e) {
                $this->property = null;
            }
        }

        return $this->property;
    }
}
