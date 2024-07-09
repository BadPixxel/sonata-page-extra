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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;

#[ORM\Entity]
#[ORM\Table(name: 'page__redirection')]
class PageRedirection
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    /**
     * Source Uri
     */
    #[ORM\Column(type: Types::STRING, nullable: false)]
    private string $uri;

    /**
     * Redirection Type
     */
    #[ORM\Column(type: Types::STRING, nullable: false)]
    private int $code = Response::HTTP_MOVED_PERMANENTLY;

    /**
     * Target Sonata Page
     */
    #[ORM\ManyToOne(targetEntity: ExtendedPage::class, inversedBy: 'redirections')]
    #[ORM\JoinColumn(name: 'page_id', referencedColumnName: 'id')]
    private ExtendedPage $page;

    /**
     * Get Redirection ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get Redirection Uri
     */
    public function getUri(): string
    {
        return $this->uri ?? "";
    }

    /**
     * Set Redirection URI
     */
    public function setUri(string $uri): static
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get Redirection HTTP Code
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Set Redirection HTTP Code
     */
    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get Target Page
     */
    public function getPage(): ExtendedPage
    {
        return $this->page;
    }

    /**
     * Set Target Page
     */
    public function setPage(ExtendedPage $page): static
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Check if Permanent Redirection
     */
    public function isPermanent(): bool
    {
        return Response::HTTP_MOVED_PERMANENTLY == $this->getCode();
    }
}
