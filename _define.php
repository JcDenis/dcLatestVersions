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

$this->registerModule(
    "Dotclear's latest versions",
    'Show the latest available versions of Dotclear',
    'Jean-Christian Denis, Pierre Van Glabeke',
    '2022.12.20',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_USAGE,
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
        'type'        => 'plugin',
        'support'     => 'http://forum.dotclear.org/viewforum.php?id=16',
        'details'     => 'http://plugins.dotaddict.org/dc2/details/' . basename(__DIR__),
        'repository'  => 'https://raw.githubusercontent.com/JcDenis/' . basename(__DIR__) . '/master/dcstore.xml',
    ]
);
