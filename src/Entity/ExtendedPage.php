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

namespace BadPixxel\SonataPageExtra\Entity;

use BadPixxel\SonataPageExtra\Interfaces\RedirectionAwarePageInterface;
use BadPixxel\SonataPageExtra\Interfaces\SeoAwarePageInterface;
use BadPixxel\SonataPageExtra\Model\Page\RedirectionsAwarePageTrait;
use BadPixxel\SonataPageExtra\Model\Page\SeoAwarePageTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sonata\PageBundle\Entity\BasePage;

#[ORM\Entity]
#[ORM\Table(name: 'page__page')]
class ExtendedPage extends BasePage implements SeoAwarePageInterface, RedirectionAwarePageInterface
{
    use SeoAwarePageTrait;
    use RedirectionsAwarePageTrait;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    protected $id;

    public function __construct()
    {
        $this->initRedirections();
        parent::__construct();
    }
}
