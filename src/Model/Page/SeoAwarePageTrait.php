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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Implement Seo Aware Page Interface
 */
trait SeoAwarePageTrait
{
    /**
     * Mark this page for Indexing
     */
    #[ORM\Column(type: Types::BOOLEAN, options: array('default' => 1))]
    protected bool $indexed;

    /**
     * @var null|array
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $metaExtras = array();

    /**
     * @inheritdoc
     */
    public function isIndexed(): bool
    {
        return $this->indexed;
    }

    /**
     * @inheritdoc
     */
    public function setIndexed(bool $indexed): static
    {
        $this->indexed = $indexed;

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
