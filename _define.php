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

$this->registerModule(
    'dcLatestVersions',
    'Show the latest available versions of Dotclear',
    'Jean-Christian Denis, Pierre Van Glabeke',
    '2022.11.12',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_USAGE,
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
        'type'       => 'plugin',
        'support'    => 'http://forum.dotclear.org/viewforum.php?id=16',
        'details'    => 'http://plugins.dotaddict.org/dc2/details/dcLatestVersions',
        'repository' => 'https://raw.githubusercontent.com/JcDenis/dcLatestVersions/master/dcstore.xml',
    ]
);
