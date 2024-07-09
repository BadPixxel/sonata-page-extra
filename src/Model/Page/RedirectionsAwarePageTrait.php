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

namespace BadPixxel\SonataPageExtra\Model\Page;

use BadPixxel\SonataPageExtra\Entity\PageRedirection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * Implement Redirections Aware Page Interface
 */
trait RedirectionsAwarePageTrait
{
    /**
     * One Sonata Page May have Many Redirections.
     *
     * @var Collection<PageRedirection>
     */
    #[ORM\OneToMany(
        targetEntity: PageRedirection::class,
        mappedBy: 'page',
        cascade: array("all"),
        orphanRemoval: true
    )]
    #[Serializer\Ignore]
    private Collection $redirections;

    /**
     * @inheritdoc
     */
    public function initRedirections(): static
    {
        $this->redirections = new ArrayCollection();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRedirections(): Collection
    {
        return $this->redirections;
    }

    /**
     * @inheritdoc
     */
    public function addRedirection(PageRedirection $redirection): static
    {
        $this->redirections->add($redirection);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeRedirection(PageRedirection $redirection): static
    {
        $this->redirections->removeElement($redirection);

        return $this;
    }
}
