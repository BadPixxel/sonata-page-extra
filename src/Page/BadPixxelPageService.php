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

namespace BadPixxel\SonataPageExtra\Page;

use BadPixxel\SonataPageExtra\Interfaces\PageConfiguratorInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Page\Service\BasePageService;
use Sonata\PageBundle\Page\TemplateManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/**
 * Splash page service to render a page template.
 */
#[AutoconfigureTag("sonata.page")]
class BadPixxelPageService extends BasePageService
{
    const NAME = 'Extended Page';

    /**
     * Service Constructor.
     *
     * @param PageConfiguratorInterface[] $configurators
     */
    public function __construct(
        #[TaggedIterator(PageConfiguratorInterface::TAG)]
        private readonly iterable $configurators,
        private readonly TemplateManagerInterface $templateManager,
    ) {
        parent::__construct(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(
        PageInterface $page,
        Request $request,
        array $parameters = array(),
        Response $response = null
    ): Response {
        //==============================================================================
        // Execute Page Configurators
        foreach ($this->configurators as $configurator) {
            Assert::isInstanceOf($configurator, PageConfiguratorInterface::class);

            if ($configurator->handle($page)) {
                Assert::true(
                    $configurator->configure($page, $request, $parameters)
                );
            }
        }

        return $this->templateManager->renderResponse(
            (string) $page->getTemplateCode(),
            $parameters,
            $response
        );
    }
}
