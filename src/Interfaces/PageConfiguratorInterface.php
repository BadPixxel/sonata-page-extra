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

namespace BadPixxel\SonataPageExtra\Interfaces;

use Sonata\PageBundle\Model\PageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for All Sonata Page Tagged Configurators
 */
interface PageConfiguratorInterface
{
    /**
     * Symfony Service Tag for Page Configurators
     */
    const TAG = "badpixxel.sonata.page.configurator";

    /**
     * Check if this Configurator Manage this Page type
     */
    public function handle(PageInterface $page): bool;

    /**
     * Configurator Page before Rendering
     */
    public function configure(PageInterface $page, Request $request, array $parameters = array()): bool;
}
