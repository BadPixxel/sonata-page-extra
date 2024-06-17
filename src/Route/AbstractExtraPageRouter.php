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

namespace BadPixxel\SonataPageExtra\Route;

use BadPixxel\SonataPageExtra\Services\WebsiteManager;
use Sonata\PageBundle\CmsManager\CmsManagerInterface;
use Sonata\PageBundle\Route\CmsPageRouter;
use Symfony\Cmf\Component\Routing\ChainedRouterInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Extend Sonata CMS Router to Introduce new Features
 */
#[Autoconfigure(bind: array(
    '$cmsRouter' => "@sonata.page.router",
    '$cmsManager' => "@sonata.page.cms.page",
))]
abstract class AbstractExtraPageRouter implements ChainedRouterInterface
{
    public function __construct(
        protected readonly CmsPageRouter       $cmsRouter,
        protected readonly CmsManagerInterface $cmsManager,
        protected readonly WebsiteManager      $hostsManager,
    ) {
    }

    //==============================================================================
    // Forward Features to Generic Cms Page Router
    //==============================================================================

    /**
     * @inheritdoc
     */
    public function setContext(RequestContext $context): void
    {
        $this->cmsRouter->setContext($context);
    }

    /**
     * @inheritdoc
     */
    public function getContext(): RequestContext
    {
        return $this->cmsRouter->getContext();
    }

    /**
     * @inheritdoc
     */
    public function getRouteCollection(): RouteCollection
    {
        return $this->cmsRouter->getRouteCollection();
    }

    /**
     * @inheritdoc
     */
    public function supports(mixed $name): bool
    {
        return $this->cmsRouter->supports($name);
    }

    /**
     * @inheritdoc
     *
     * @param string $name
     */
    public function getRouteDebugMessage($name, array $parameters = array()): string
    {
        return $this->cmsRouter->getRouteDebugMessage($name, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function match($pathinfo): array
    {
        return $this->cmsRouter->match($pathinfo);
    }
}
