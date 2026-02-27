<?php
/**
 * Copyright (C) 2014-2020 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

if ( ai1wm_got_url_rewrite() ) {
	printf(
		__(
			'» <a class="ai1wm-no-underline" href="%s" target="_blank">Save permalinks structure</a>. (opens a new window)<br />' .
			'» <a class="ai1wm-no-underline" href="https://oxygenbuilder.com/documentation/other/importing-exporting/#resigning" target="_blank">Re-sign Oxygen Builder shortcodes</a>. (opens a new window)<br />' .
			'» <a class="ai1wm-no-underline" href="https://wordpress.org/support/view/plugin-reviews/all-in-one-wp-migration?rate=5#postform" target="_blank">Optionally, review the plugin</a>. (opens a new window)',
			AI1WM_PLUGIN_NAME
		),
		admin_url( 'options-permalink.php#submit' )
	);
} else {
	_e(
		'» Permalinks are set to default. <a class="ai1wm-no-underline" href="https://help.servmask.com/knowledgebase/permalinks-are-set-to-default/" target="_blank">Why?</a> (opens a new window)<br />' .
		'» <a class="ai1wm-no-underline" href="https://oxygenbuilder.com/documentation/other/importing-exporting/#resigning" target="_blank">Re-sign Oxygen Builder shortcodes</a>. (opens a new window)<br />' .
		'» <a class="ai1wm-no-underline" href="https://wordpress.org/support/view/plugin-reviews/all-in-one-wp-migration?rate=5#postform" target="_blank">Optionally, review the plugin</a>. (opens a new window)',
		AI1WM_PLUGIN_NAME
	);
}
