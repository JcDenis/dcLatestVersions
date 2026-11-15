<?php

declare(strict_types=1);

namespace Dotclear\Plugin\dcLatestVersions;

use Dotclear\App;
use Dotclear\Core\Upgrade\Update;

/**
 * @brief       dcLatestVersions versions helper.
 * @ingroup     dcLatestVersions
 *
 * @author      Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Versions
{
    /**
     * Delay in seconds before trying again an unreachable server.
     */
    public const RETRY_DELAY = 3600;

    /**
     * Get builds from a comma separated list.
     *
     * @return  array<int, string>
     */
    public static function builds(string $list): array
    {
        $builds = [];
        foreach (explode(',', $list) as $build) {
            $build = strtolower(trim($build));
            if ($build !== '') {
                $builds[] = $build;
            }
        }

        return $builds;
    }

    /**
     * Get latest version of a build.
     *
     * Server is not queried again before a delay if it was unreachable.
     *
     * @return  null|array{version: string, url: string}
     */
    public static function get(string $build): ?array
    {
        $cache_dir = App::config()->cacheRoot() . '/versions';
        $fail_file = $cache_dir . '/' . My::id() . '-' . md5($build) . '.fail';
        if (is_file($fail_file) && (int) filemtime($fail_file) > time() - self::RETRY_DELAY) {
            return null;
        }

        $updater = new Update(App::config()->coreUpdateUrl(), 'dotclear', $build, $cache_dir);
        $updater->check('0');

        $version = (string) $updater->getVersion();
        $url     = (string) $updater->getFileURL();
        if ($version === '' || $url === '') {
            if (is_dir($cache_dir) && is_writable($cache_dir)) {
                @touch($fail_file);
            }

            return null;
        }

        if (is_file($fail_file)) {
            @unlink($fail_file);
        }

        return ['version' => $version, 'url' => $url];
    }
}
