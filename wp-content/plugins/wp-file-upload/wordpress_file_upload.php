<?php
/*Plugin Name: Wordpress File Upload
/*
Plugin URI: http://www.iptanus.com/support/wordpress-file-upload
Description: Simple interface to upload files from a page.
Version: 4.10.0
Author: Nickolas Bossinas
Author URI: http://www.iptanus.com
Text Domain: wp-file-upload
Domain Path: /languages

Wordpress File Upload (Wordpress Plugin)
Copyright (C) 2010-2018 Nickolas Bossinas
Contact me at http://www.iptanus.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

//do not load plugin if this is the login page
$uri = $_SERVER['REQUEST_URI'];
if ( strpos($uri, 'wp-login.php') !== false ) return;

//before loading the plugin we need to check if restricted loading is enabled
if ( !is_admin() ) {
	$page = get_page_by_path($uri);
	if ( $page ) {
		$envars = get_option("wfu_environment_variables", array());
		$ids = ( isset($envars["WFU_RESTRICT_FRONTEND_LOADING"]) ? $envars["WFU_RESTRICT_FRONTEND_LOADING"] : "false" );
		//if restricted loading is enabled, then the plugin will load only if
		//the current page ID is included in $ids list
		if (  $ids !== "false" ) {
			$ids = explode(",", $ids);
			$pass = false;
			foreach ( $ids as $id )
				if ( trim($id) != "" && (int)trim($id) > 0 && (int)trim($id) == $page->ID ) {
					$pass = true;
					break;
				}
			if ( !$pass ) return;
		}
	}
}
//proceed loading the plugin
DEFINE("WPFILEUPLOAD_PLUGINFILE", __FILE__);
require_once( plugin_dir_path( WPFILEUPLOAD_PLUGINFILE ) . 'wfu_loader.php' );

?>