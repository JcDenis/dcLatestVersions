<?php
/**
 * @file
 * @brief       The plugin dcLatestVersions definition
 * @ingroup     dcLatestVersions
 *
 * @defgroup    dcLatestVersions Plugin dcLatestVersions.
 *
 * Show the latest available versions of Dotclear.
 *
 * @author      Jean-Christian Denis
 * @copyright   Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
$this->registerModule(
    "Dotclear's latest versions",
    'Show the latest available versions of Dotclear',
    'Jean-Christian Denis, Pierre Van Glabeke',
    '2025.03.02',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://github.com/JcDenis/' . $this->id . '/issues',
        'details'     => 'https://github.com/JcDenis/' . $this->id . '/',
        'repository'  => 'https://raw.githubusercontent.com/JcDenis/' . $this->id . '/master/dcstore.xml',
        'date'        => '2025-02-24T23:31:12+00:00',
    ]
);
