<?php

declare(strict_types=1);

namespace Dotclear\Plugin\dcLatestVersions;

use ArrayObject;
use Dotclear\App;
use Dotclear\Core\Backend\Update;
use Dotclear\Core\Process;
use Dotclear\Helper\Html\Form\{ Checkbox, Div, Label, Li, Link, Para, Text, Ul };
use Dotclear\Helper\Html\Html;

/**
 * @brief       dcLatestVersions backend class.
 * @ingroup     dcLatestVersions
 *
 * @author      Jean-Christian Denis
 * @copyright   Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Backend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        App::behavior()->addBehaviors([
            'initWidgets'           => Widgets::initWidgets(...),
            'adminDashboardItemsV2' => function (ArrayObject $__dashboard_items): void {
                if (!App::blog()->isDefined()
                    || !My::prefs()->get('dashboard_items')
                ) {
                    return;
                }

                $builds = explode(',', (string) My::settings()->get('builds'));
                $li     = [];

                foreach ($builds as $build) {
                    $build = strtolower(trim($build));
                    if ($build === '') {
                        continue;
                    }

                    $updater = new Update(
                        App::config()->coreUpdateUrl(),
                        'dotclear',
                        $build,
                        App::config()->cacheRoot() . '/versions'
                    );

                    if ($updater->check('0') === false) {
                        continue;
                    }

                    $li[] = (new Li())
                        ->separator(' : ')
                        ->items([
                            (new Link())
                                ->href((string) $updater->getFileURL())
                                ->title(sprintf(__('Download Dotclear %s'), (string) $updater->getVersion()))
                                ->text($build),
                            (new Text('', $updater->getVersion())),
                        ]);
                }

                if ($li === []) {
                    return;
                }

                # Display
                $__dashboard_items[0][] = (new Div('udclatestversionsitems'))
                    ->class(['box', 'small'])
                    ->items([
                        (new Text('h3', Html::escapeHTML(My::name()))),
                        (new Ul())
                            ->items($li),
                    ])
                    ->render();
            },

            'adminDashboardOptionsFormV2' => function (): void {
                if (!My::prefs()->prefExists('dashboard_items')) {
                    My::prefs()->put(
                        'dashboard_items',
                        false,
                        'boolean'
                    );
                }

                echo (new Div())
                    ->class('fieldset')
                    ->items([
                        (new Text('h4', Html::escapeHTML(My::name()))),
                        (new Para())
                            ->items([
                                (new Checkbox(My::id() . 'dashboard_items', (bool) My::prefs()->get('dashboard_items')))
                                    ->value(1)
                                    ->label(new Label(__("Show Dotclear's latest versions on dashboards."), Label::IL_FT)),
                            ]),
                    ])
                    ->render();
            },

            'adminAfterDashboardOptionsUpdate' => function (?string $user_id): void {
                My::prefs()->put(
                    'dashboard_items',
                    !empty($_POST[My::id() . 'dashboard_items']),
                    'boolean'
                );
            },
        ]);

        return true;
    }
}
