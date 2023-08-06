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
use dcUpdate;
use Dotclear\Core\Process;
use Dotclear\Helper\Html\Form\{
    Checkbox,
    Label,
    Para
};
use Dotclear\Helper\Html\Html;

class Backend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        dcCore::app()->addBehaviors([
            'initWidgets'           => [Widgets::class, 'initWidgets'],
            'adminDashboardItemsV2' => function (ArrayObject $__dashboard_items): void {
                // nullsafe PHP < 8.0
                if (is_null(dcCore::app()->blog)) {
                    return;
                }

                if (!My::prefs()->get('dashboard_items')) {
                    return;
                }

                $builds = explode(',', (string) My::settings()->get('builds'));
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
                if (!My::prefs()->prefExists('dashboard_items')) {
                    My::prefs()->put(
                        'dashboard_items',
                        false,
                        'boolean'
                    );
                }

                echo
                '<div class="fieldset">' .
                '<h4>' . Html::escapeHTML(My::name()) . '</h4>' .
                (new Para())
                    ->__call('items', [[
                        (new Checkbox(My::id() . 'dashboard_items', (bool) My::prefs()->get('dashboard_items')))
                            ->__call('value', [1]),
                        (new Label(__("Show Dotclear's latest versions on dashboards."), Label::OUTSIDE_LABEL_AFTER))
                            ->__call('for', [My::id() . 'dashboard_items'])
                            ->__call('class', ['classic']),
                    ]])
                    ->render() .
                '</div>';
            },

            'adminAfterDashboardOptionsUpdate' => function (?string $user_id): void {
                My::prefs()->put(
                    'dashboard_items',
                    !empty($_POST[My::id() . 'dashboard_items']),
                    'boolean'
                );
            },
        ]);

        return true;
    }
}
