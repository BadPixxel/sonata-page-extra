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

namespace BadPixxel\SonataPageExtra\Dictionary;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class defining possible redirection types.
 */
class RedirectTypes
{
    /**
     * Redirections Type Codes.
     */
    const ALL = array(
        'Permanent' => Response::HTTP_MOVED_PERMANENTLY,
        'Temporary' => Response::HTTP_TEMPORARY_REDIRECT,
    );
}
