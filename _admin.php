<?php
/**
 * @brief dcLatestVersions, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Jean-Christian Denis, Pierre Van Glabeke
 *
 * @copyright Jean-Crhistian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_CONTEXT_ADMIN')) {
    return null;
}

require __DIR__ . '/_widgets.php';

dcCore::app()->addBehavior('adminDashboardItemsV2', function($__dashboard_items) {
    if (!dcCore::app()->auth->user_prefs->dashboard->get('dcLatestVersionsItems')) {
        return null;
    }

    $builds = explode(',', (string) dcCore::app()->blog->settings->dcLatestVersions->builds);
    if (empty($builds)) {
        return null;
    }

    $text = __('<li><a href="%u" title="Download Dotclear %v">%r</a> : %v</li>');
    $li   = [];

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

        $li[] = str_replace(
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
            $text
        );
    }

    if (empty($li)) {
        return null;
    }

    # Display
    $__dashboard_items[0][] = '<div class="box small" id="udclatestversionsitems">' .
    '<h3>' . html::escapeHTML(__("Dotclear's latest versions")) . '</h3>' .
    '<ul>' . implode('', $li) . '</ul>' .
    '</div>';
});

dcCore::app()->addBehavior('adminDashboardOptionsFormV2', function() {
    if (!dcCore::app()->auth->user_prefs->dashboard->prefExists('dcLatestVersionsItems')) {
        dcCore::app()->auth->user_prefs->dashboard->put(
            'dcLatestVersionsItems',
            false,
            'boolean'
        );
    }
    $pref = dcCore::app()->auth->user_prefs->dashboard->get('dcLatestVersionsItems');

    echo
    '<div class="fieldset">' .
    '<h4>' . __("Dotclear's latest versions") . '</h4>' .
    '<p><label class="classic" for="dcLatestVersionsItems">' .
    form::checkbox('dcLatestVersionsItems', 1, $pref) . ' ' .
    __("Show Dotclear's latest versions on dashboards.") .
    '</label></p>' .
    '</div>';
});

dcCore::app()->addBehavior('adminAfterDashboardOptionsUpdate', function($user_id) {
    dcCore::app()->auth->user_prefs->dashboard->put(
        'dcLatestVersionsItems',
        !empty($_POST['dcLatestVersionsItems']),
        'boolean'
    );
});
