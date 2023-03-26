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
if (!defined('DC_RC_PATH')) {
    return null;
}

dcCore::app()->blog->settings->addNamespace(basename(__DIR__));

dcCore::app()->addBehavior(
    'initWidgets',
    ['dcLatestVersionsWidget', 'adminWidget']
);

/**
 * @ingroup DC_PLUGIN_DCLATESTVERSIONS
 * @brief Display latest versions of Dotclear - widget methods.
 * @since 2.6
 */
class dcLatestVersionsWidget
{
    public static function adminWidget($w)
    {
        $w
            ->create(
                'dclatestversionswidget',
                __("Dotclear's latest versions"),
                ['dcLatestVersionsWidget','publicWidget'],
                null,
                __('Show the latest available versions of Dotclear')
            )
            ->addTitle(
                __("Dotclear's latest versions")
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

    public static function publicWidget($w)
    {
        if ($w->offline) {
            return null;
        }

        if (!$w->checkHomeOnly(dcCore::app()->url->type) || $w->text == '') {
            return null;
        }

        # Builds to check
        $builds = explode(',', (string) dcCore::app()->blog->settings->get(basename(__DIR__))->get('builds'));
        if (empty($builds[0])) {
            return null;
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
            return null;
        }

        # Display
        return $w->renderDiv(
            $w->content_only,
            'dclatestversionswidget ' . $w->class,
            '',
            ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '') . sprintf('<ul>%s</ul>', implode('', $li))
        );
    }
}
