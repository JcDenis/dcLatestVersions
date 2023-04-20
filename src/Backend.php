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

use ArrayObject;
use dcCore;
use dcNsProcess;
use dcUpdate;
use Dotclear\Helper\Html\Form\{
    Checkbox,
    Label,
    Para
};
use Dotclear\Helper\Html\Html;

class Backend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN');

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->addBehaviors([
            'initWidgets'           => [Widgets::class, 'initWidgets'],
            'adminDashboardItemsV2' => function (ArrayObject $__dashboard_items): void {
                // nullsafe PHP < 8.0
                if (is_null(dcCore::app()->auth) || is_null(dcCore::app()->auth->user_prefs) || is_null(dcCore::app()->blog)) {
                    return;
                }

                if (!dcCore::app()->auth->user_prefs->get('dashboard')->get('dcLatestVersionsItems')) {
                    return;
                }

                $builds = explode(',', (string) dcCore::app()->blog->settings->get(My::id())->get('builds'));
                if (empty($builds[0])) {
                    return;
                }

                $li = [];

                foreach ($builds as $build) {
                    $build = strtolower(trim($build));
                    if (empty($build)) {
                        continue;
                    }

                    $updater = new dcUpdate(
                        DC_UPDATE_URL,
                        'dotclear',
                        $build,
                        DC_TPL_CACHE . '/versions'
                    );

                    if (false === $updater->check('0')) {
                        continue;
                    }

                    $li[] = sprintf(
                        '<li><a href="%1$s" title="%2$s">%3$s</a> : %4$s</li>',
                        $updater->getFileURL(),
                        sprintf(__('Download Dotclear %s'), $updater->getVersion()),
                        $build,
                        $updater->getVersion()
                    );
                }

                if (empty($li)) {
                    return;
                }

                # Display
                $__dashboard_items[0][] = '<div class="box small" id="udclatestversionsitems">' .
                '<h3>' . Html::escapeHTML(My::name()) . '</h3>' .
                '<ul>' . implode('', $li) . '</ul>' .
                '</div>';
            },

            'adminDashboardOptionsFormV2' => function (): void {
                // nullsafe PHP < 8.0
                if (is_null(dcCore::app()->auth) || is_null(dcCore::app()->auth->user_prefs)) {
                    return;
                }

                if (!dcCore::app()->auth->user_prefs->get('dashboard')->prefExists('dcLatestVersionsItems')) {
                    dcCore::app()->auth->user_prefs->get('dashboard')->put(
                        'dcLatestVersionsItems',
                        false,
                        'boolean'
                    );
                }

                echo
                '<div class="fieldset">' .
                '<h4>' . Html::escapeHTML(My::name()) . '</h4>' .
                (new Para())->items([
                    (new Checkbox('dcLatestVersionsItems', (bool) dcCore::app()->auth->user_prefs->get('dashboard')->get('dcLatestVersionsItems')))->value(1),
                    (new Label(__("Show Dotclear's latest versions on dashboards."), Label::OUTSIDE_LABEL_AFTER))->for('dcLatestVersionsItems')->class('classic'),
                ])->render() .
                '</div>';
            },

            'adminAfterDashboardOptionsUpdate' => function (?string $user_id): void {
                // nullsafe PHP < 8.0
                if (is_null(dcCore::app()->auth) || is_null(dcCore::app()->auth->user_prefs)) {
                    return;
                }

                dcCore::app()->auth->user_prefs->get('dashboard')->put(
                    'dcLatestVersionsItems',
                    !empty($_POST['dcLatestVersionsItems']),
                    'boolean'
                );
            },
        ]);

        return true;
    }
}
