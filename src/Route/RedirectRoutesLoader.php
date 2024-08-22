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

namespace BadPixxel\SonataPageExtra\Route;

use BadPixxel\SonataPageExtra\Entity\PageRedirection;
use BadPixxel\SonataPageExtra\Helpers\RedirectRouteBuilder;
use Doctrine\DBAL\Exception\ServerException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Routing\RouteCollection;
use Webmozart\Assert\Assert;

#[Autoconfigure(bind: array(
    '$debug' => "%kernel.debug%"
))]
class RedirectRoutesLoader implements RouteLoaderInterface
{
    public function __construct(
        private readonly bool $debug,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(): RouteCollection
    {
        //==============================================================================
        // Create an Empty Route Collection
        $routeCollection = new RouteCollection();
        //==============================================================================
        // Walk on Defined Redirections
        foreach ($this->getRedirections() as $pageRedirection) {
            //==============================================================================
            // Build Redirect Route
            $route = RedirectRouteBuilder::fromPageRedirection($pageRedirection);
            //==============================================================================
            // Disable Permanent Redirects in debug Mode
            if ($this->debug) {
                $route->setDefault('permanent', $pageRedirection->isPermanent());
            }
            //==============================================================================
            // Build Target Page Name
            $site = $pageRedirection->getPage()->getSite();
            Assert::notEmpty($site);
            $targetPage = sprintf(
                "[Redirect] %s | %s",
                $site->getName(),
                $pageRedirection->getPage()->getName() ?: $pageRedirection->getPage()->getId()
            );
            //==============================================================================
            // Add Route to Collection
            $routeCollection->add($targetPage, $route);
        }

        return $routeCollection;
    }

    /**
     * Load List of Redirections from Database
     *
     * @return PageRedirection[]
     */
    private function getRedirections(): array
    {
        try {
            $redirections = $this->entityManager->getRepository(PageRedirection::class)->findAll();
            Assert::allIsInstanceOf($redirections, PageRedirection::class);

            return $redirections;
        } catch (ServerException $e) {
            return array();
        }
    }
}
