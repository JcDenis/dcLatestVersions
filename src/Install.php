<?php
/**
 * @brief dcLatestVersions, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Jean-Christian Denis, Pierre Van Glabeke
 *
 * @copyright Jean-Christian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\dcLatestVersions;

use dcCore;
use dcNsProcess;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN')
            && dcCore::app()->newVersion(My::id(), dcCore::app()->plugins->moduleInfo(My::id(), 'version'));

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        // nullsafe PHP < 8.0
        if (is_null(dcCore::app()->blog)) {
            return false;
        }

        dcCore::app()->blog->settings->get(My::id())->put(
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
