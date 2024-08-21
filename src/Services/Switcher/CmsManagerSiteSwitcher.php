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
use Sonata\PageBundle\CmsManager\CmsManagerInterface;
use Sonata\PageBundle\CmsManager\CmsManagerSelectorInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Webmozart\Assert\Assert;

/**
 * Enable Using Sonata Page Manager with Different Site/Hosts Contexts
 */
#[Autoconfigure(bind: array(
    '$cmsPageManagerSelector' => "@sonata.page.cms_manager_selector",
))]
class CmsManagerSiteSwitcher
{
    /**
     * Storage for Page References Reflexion Property
     */
    private ?ReflectionProperty $property = null;

    /**
     * Default Website
     */
    private ?SiteInterface $defaultSite = null;

    /**
     * Currently Used Website
     */
    private ?SiteInterface $currentSite = null;

    /**
     * @var array[]
     */
    private array $pageReferences = array();

    public function __construct(
        private readonly CmsManagerSelectorInterface $cmsPageManagerSelector
    ) {
    }

    public function setDefaultSite(?SiteInterface $site): static
    {
        $this->defaultSite = $site;

        return $this;
    }

    /**
     *
     */
    public function switchTo(SiteInterface $site): void
    {
        Assert::notEmpty($property = $this->getProperty());
        //====================================================================//
        // Switch Only if Different
        if ($this->defaultSite && ($this->defaultSite->getId() == $site->getId())) {
            return;
        }
        //====================================================================//
        // Store Default Cms Manager Page References Cache
        if ($this->defaultSite) {
            Assert::isArray($pageReferences = $property->getValue($this->getManager()));
            $this->pageReferences[$this->defaultSite->getId()] = $pageReferences;
        }
        //====================================================================//
        // Ensure Init of Cms Manager Page References for Website
        $this->pageReferences[$site->getId()] ??= array(
            'url' => array(),
            'routeName' => array(),
            'pageAlias' => array(),
            'name' => array(),
        );
        //====================================================================//
        // Replace Cms Manager Page References for Website
        $this->currentSite = $site;
        $property->setValue($this->getManager(), $this->pageReferences[$site->getId()]);
    }

    /**
     *
     */
    public function reset(): void
    {
        //====================================================================//
        // Switch Only if Different
        if (!$this->currentSite) {
            return;
        }
        Assert::notEmpty($property = $this->getProperty());
        Assert::keyExists($this->pageReferences, (string) $this->currentSite->getId(), "Current site doesn't exist.");
        //====================================================================//
        // Store Current Cms Manager Page References Cache
        Assert::isArray($pageReferences = $property->getValue($this->getManager()));
        $this->pageReferences[$this->currentSite->getId()] = $pageReferences;
        //====================================================================//
        // Reset Cms Manager Page References for Website
        if ($this->defaultSite) {
            $property->setValue($this->getManager(), $this->pageReferences[$this->defaultSite->getId()]);
        }
        $this->currentSite = null;
    }

    /**
     * Get Site Selector Site Reflexion Property
     */
    private function getManager(): CmsManagerInterface
    {
        return $this->cmsPageManagerSelector->retrieve();
    }

    /**
     * Get Site Selector Site Reflexion Property
     */
    private function getProperty(): ?ReflectionProperty
    {
        if (!isset($this->property)) {
            try {
                $reflexionClass = new ReflectionClass($this->getManager());
                $this->property = $reflexionClass->getProperty("pageReferences");
                $this->property->setAccessible(true);
            } catch (ReflectionException) {
                $this->property = null;
            }
        }

        return $this->property;
    }
}
