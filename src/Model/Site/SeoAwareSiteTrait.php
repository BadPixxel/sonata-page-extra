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

namespace BadPixxel\SonataPageExtra\Model\Site;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Implement Seo Aware Page Interface
 */
trait SeoAwareSiteTrait
{
    /**
     * Extra Contents to Add on robots.txt
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $robotsExtras = null;

    /**
     * @var null|array
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $metaExtras = array();

    /**
     * Get Site Robots.txt Extra Contents
     */
    public function getRobotsExtra(): string
    {
        return (string) $this->robotsExtras;
    }

    /**
     * Set Site Robots.txt Extra Contents
     */
    public function setRobotsExtra(?string $contents = null): static
    {
        $this->robotsExtras = $contents;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMetaExtra(): array
    {
        return $this->metaExtras ?? array();
    }

    /**
     * @inheritdoc
     */
    public function setMetaExtra(array $metadata): static
    {
        $this->metaExtras = $metadata;

        return $this;
    }
}
