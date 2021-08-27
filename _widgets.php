<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of dcLatestVersions, a plugin for Dotclear 2.
# 
# Copyright (c) 2009-2021 Jean-Christian Denis and contributors
# 
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) {
    return null;
}

$core->blog->settings->addNamespace('dcLatestVersions');

$core->addBehavior(
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
                __("Show the latest available versions of Dotclear")
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
        global $core;

        $core->blog->settings->addNamespace('dcLatestVersions');

        if ($w->offline) {
            return null;
        }

        if (($w->homeonly == 1 && !$core->url->isHome($core->url->type)) 
         || ($w->homeonly == 2 && $core->url->isHome($core->url->type))
         || $w->text == '') {
            return null;
        }

        # Builds to check
        $builds = (string) $core->blog->settings->dcLatestVersions->builds;
        $builds = explode(',', $builds);
        if (empty($builds)) {
            return null;
        }

        $li = [];
        foreach($builds as $build) {

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
                    '%u'
                ],
                [
                    $build,
                    $updater->getVersion(),
                    $updater->getFileURL()
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
            'dclatestversionswidget '. $w->class, 
            '', 
            ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '') . sprintf('<ul>%s</ul>', implode('',$li))
        );
    }
}