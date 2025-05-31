<?php

declare(strict_types=1);

namespace Dotclear\Plugin\dcLatestVersions;

use Dotclear\App;
use Dotclear\Core\Backend\Update;
use Dotclear\Helper\Html\Form\{ Li, Ul };
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\{ WidgetsStack, WidgetsElement };

/**
 * @brief       dcLatestVersions widgets class.
 * @ingroup     dcLatestVersions
 *
 * @author      Jean-Christian Denis
 * @copyright   Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Widgets
{
    public static function initWidgets(WidgetsStack $w): void
    {
        $w
            ->create(
                My::id() . 'widget',
                My::name(),
                self::parseWidget(...),
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
        if ($w->__get('offline')
            || !$w->checkHomeOnly(App::url()->getType())
            || $w->__get('text') == ''
            || !App::blog()->isDefined()
        ) {
            return '';
        }

        # Builds to check
        $builds = explode(',', (string) My::settings()->get('builds'));
        if (empty($builds[0])) {
            return '';
        }

        $li = [];
        foreach ($builds as $build) {
            $build = strtolower(trim($build));
            if (empty($build)) {
                continue;
            }

            $updater = new Update(
                App::config()->coreUpdateUrl(),
                'dotclear',
                $build,
                App::config()->cacheRoot() . '/versions'
            );

            if (false === $updater->check('0')) {
                continue;
            }

            $li[] = (new Li())->text((string) str_replace(
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
                $w->__get('text')
            ));
        }

        if ($li === []) {
            return '';
        }

        # Display
        return $w->renderDiv(
            (bool) $w->__get('content_only'),
            My::id() . 'widget ' . $w->__get('class'),
            '',
            ($w->__get('title') ? $w->renderTitle(Html::escapeHTML($w->__get('title'))) : '') . (new Ul())->items($li)->render()
        );
    }
}
