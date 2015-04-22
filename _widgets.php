<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of dcLatestVersions, a plugin for Dotclear 2.
# 
# Copyright (c) 2009-2015 Jean-Christian Denis and contributors
# contact@jcdenis.fr http://jcd.lv
# 
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) {

	return null;
}

$core->blog->settings->addNamespace('dcLatestVersions');

$core->addBehavior(
	'initWidgets',
	array('dcLatestVersionsWidget', 'adminWidget')
);

/**
 * @ingroup DC_PLUGIN_DCLATESTVERSIONS
 * @brief Display latest versions of Dotclear - widget methods.
 * @since 2.6
 */
class dcLatestVersionsWidget
{
	public static function adminWidget($w)
	{
		$w->create(
			'dclatestversionswidget',
			__("Dotclear's latest versions"),
			array('dcLatestVersionsWidget','publicWidget'),
			null,
			__("Show the latest available versions of Dotclear")
		);
		$w->dclatestversionswidget->setting(
			'title',
			__('Title:'),
			__("Dotclear's latest versions"),
			'text'
		);
		$w->dclatestversionswidget->setting(
			'text',
			__('Text (%r = release, %v = version, %u = url):'),
			__('<strong>%r: </strong> <a href="%u" title="Download Dotclear %v">%v</a>'),
			'text'
		);
		$w->dclatestversionswidget->setting(
			'homeonly',
			__('Display on:'),
			0,
			'combo',
			array(
				__('All pages') => 0, 
				__('Home page only') => 1, 
				__('Except on home page') => 2
			)
		);
		$w->dclatestversionswidget->setting(
			'content_only',
			__('Content only'),
			0,
			'check'
		);
		$w->dclatestversionswidget->setting(
			'class',
			__('CSS class:'),
			''
		);
		$w->dclatestversionswidget->setting('offline',__('Offline'),0,'check');
	}

	public static function publicWidget($w)
	{
		global $core;

		$core->blog->settings->addNamespace('dcLatestVersions');

		if ($w->offline)
			return;

		# Nothing to display
		if ($w->homeonly == 1 && $core->url->type != 'default' 
		 || $w->homeonly == 2 && $core->url->type == 'default' 
		 || $w->text == ''
		) {
			return null;
		}

		# Builds to check
		$builds = (string) $core->blog->settings->dcLatestVersions->builds;
		$builds = explode(',', $builds);
		if (empty($builds)) {

			return null;
		}

		$li = array();
		foreach($builds as $build) {

			$build = strtolower(trim($build));
			if (empty($build)) {
				continue;
			}

			$updater = new dcUpdate(
				DC_UPDATE_URL,
				'dotclear',
				$build,
				DC_TPL_CACHE.'/versions'
			);

			if (false === $updater->check('0')) {
				continue;
			}

			$li[] = '<li>'.str_replace(
				array(
					'%r',
					'%v',
					'%u'
				),
				array(
					$build,
					$updater->getVersion(),
					$updater->getFileURL()
				),
				$w->text
			).'</li>';
		}

		if (empty($li)) {

			return null;
		}

		# Display
		$res =
		($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '').
		'<ul>'.implode('',$li).'</ul>';

		return $w->renderDiv($w->content_only,'dclatestversionswidget '.$w->class,'',$res);
	}
}