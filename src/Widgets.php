<?php

declare(strict_types=1);

namespace Dotclear\Plugin\dcLatestVersions;

use Dotclear\App;
use Dotclear\Helper\Html\Form\Li;
use Dotclear\Helper\Html\Form\Ul;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsStack;
use Dotclear\Plugin\widgets\WidgetsElement;

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
                'builds',
                __('Builds to check (comma separated list):'),
                My::settings()->getStr('builds', false),
                'text'
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

        # Builds to check, from widget or from global setting
        $builds = Versions::builds(is_string($w->get('builds')) && $w->get('builds') !== '' ? $w->get('builds') : My::settings()->getStr('builds', false));

        $li = [];
        foreach ($builds as $build) {
            if (null === ($latest = Versions::get($build))) {
                continue;
            }

            $replace = str_replace(
                [
                    '%r',
                    '%v',
                    '%u',
                ],
                [
                    Html::escapeHTML($build),
                    Html::escapeHTML($latest['version']),
                    Html::escapeURL($latest['url']),
                ],
                is_string($w->get('text')) ? $w->get('text') : ''
            );

            $li[] = (new Li())->text($replace);
        }

        if ($li === []) {
            return '';
        }

        # Display
        $title = is_string($w->get('title')) ? $w->get('title') : '';

        return $w->renderDiv(
            (bool) $w->get('content_only'),
            My::id() . 'widget ' . (is_string($w->get('class')) ? $w->get('class') : ''),
            '',
            ($title !== '' ? $w->renderTitle(Html::escapeHTML($title)) : '') . (new Ul())->items($li)->render()
        );
    }
}
