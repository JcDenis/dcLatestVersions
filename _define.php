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
declare(strict_types=1);

if (!isset($this) || !is_object($this) || !method_exists($this, 'registerModule') || !isset($this->id) || !is_string($this->id)) {
    return;
}

$this->registerModule(
    "Dotclear's latest versions",
    'Show the latest available versions of Dotclear',
    'Jean-Christian Denis, Pierre Van Glabeke',
    '2026.11.09',
    [
        'requires'    => [['core', '2.39']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://github.com/JcDenis/' . $this->id . '/issues',
        'details'     => 'https://github.com/JcDenis/' . $this->id . '/',
        'repository'  => 'https://raw.githubusercontent.com/JcDenis/' . $this->id . '/master/dcstore.xml',
        'date'        => '2026-11-09T19:20:11+00:00',
    ]
);
