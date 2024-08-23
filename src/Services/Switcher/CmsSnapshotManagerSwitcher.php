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

namespace BadPixxel\SonataPageExtra\Services\Switcher;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * Enable Using Sonata Snapshot Manager with Different Site/Hosts Contexts
 */
#[Autoconfigure(bind: array(
    '$cmsPageManager' => "@sonata.page.cms.page",
))]
class CmsSnapshotManagerSwitcher extends CmsPageManagerSwitcher
{
}
