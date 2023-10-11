<?php

declare(strict_types=1);

namespace Dotclear\Plugin\dcLatestVersions;

use Dotclear\Core\Process;

/**
 * @brief   dcLatestVersions installation class.
 * @ingroup dcLatestVersions
 *
 * @author      Jean-Christian Denis
 * @copyright   Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        My::settings()->put(
            'builds',
            'stable,unstable,testing',
            'string',
            "List of Dotclear's builds",
            false,
            true
        );

        return true;
    }
}
