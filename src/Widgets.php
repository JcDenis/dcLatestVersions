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
use dcUpdate;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsStack;
use Dotclear\Plugin\widgets\WidgetsElement;

class Widgets
{
    public static function initWidgets(WidgetsStack $w): void
    {
        $w
            ->create(
                'dclatestversionswidget',
                My::name(),
                [self::class, 'parseWidget'],
                null,
                __('Show the latest available versions of Dotclear')
            )
            ->addTitle(
                My::name()
            )
            ->setting(
                'text',
                __('Text (%r = release, %v = version, %u = url):'),
                __('<strong>%r: </strong> <a href="%u" title="Download Dotclear %v">%v</a>'),
                'text'
            )
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();
    }

    public static function parseWidget(WidgetsElement $w): string
    {
        if ($w->offline || !$w->checkHomeOnly(dcCore::app()->url->type) || $w->text == '') {
            return '';
        }

        # Builds to check
        $builds = explode(',', (string) dcCore::app()->blog->settings->get(My::id())->get('builds'));
        if (empty($builds[0])) {
            return '';
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

            $li[] = sprintf('<li>%s</li>', str_replace(
                [
                    '%r',
                    '%v',
                    '%u',
                ],
                [
                    $build,
                    $updater->getVersion(),
                    $updater->getFileURL(),
                ],
                $w->text
            ));
        }

        if (empty($li)) {
            return '';
        }

        # Display
        return $w->renderDiv(
            (bool) $w->content_only,
            'dclatestversionswidget ' . $w->class,
            '',
            ($w->title ? $w->renderTitle(Html::escapeHTML($w->title)) : '') . sprintf('<ul>%s</ul>', implode('', $li))
        );
    }
}
