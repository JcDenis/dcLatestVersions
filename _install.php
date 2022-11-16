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

if (!defined('DC_CONTEXT_ADMIN')) {
    return null;
}

# -- Module specs --
$dc_min   = '2.24';
$mod_id   = 'dcLatestVersions';
$mod_conf = [[
    'builds',
    "List of Dotclear's builds",
    'stable,unstable,testing,sexy',
    'string',
]];

# -- Nothing to change below --
try {
    # Check module version
    if (version_compare(
        dcCore::app()->getVersion($mod_id),
        dcCore::app()->plugins->moduleInfo($mod_id, 'version'),
        '>='
    )) {
        return null;
    }
    # Check Dotclear version
    if (!method_exists('dcUtils', 'versionsCompare')
     || dcUtils::versionsCompare(DC_VERSION, $dc_min, '<', false)) {
        throw new Exception(sprintf(
            '%s requires Dotclear %s',
            $mod_id,
            $dc_min
        ));
    }
    # Set module settings
    dcCore::app()->blog->settings->addNamespace($mod_id);
    foreach ($mod_conf as $v) {
        dcCore::app()->blog->settings->{$mod_id}->put(
            $v[0],
            $v[2],
            $v[3],
            $v[1],
            false,
            true
        );
    }
    # Set module version
    dcCore::app()->setVersion(
        $mod_id,
        dcCore::app()->plugins->moduleInfo($mod_id, 'version')
    );

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());

    return false;
}
