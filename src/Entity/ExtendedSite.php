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

use BadPixxel\SonataPageExtra\Interfaces\SeoAwareSiteInterface;
use BadPixxel\SonataPageExtra\Model\Site\SeoAwareSiteTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sonata\PageBundle\Entity\BaseSite;

#[ORM\Entity]
#[ORM\Table(name: 'page__site')]
class ExtendedSite extends BaseSite implements SeoAwareSiteInterface
{
    use SeoAwareSiteTrait;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    protected $id;
}
