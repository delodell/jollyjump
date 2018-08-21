<?php

function wfu_uploadedfiles_menu() {
	$_GET = stripslashes_deep($_GET);
	$tag = (!empty($_GET['tag']) ? $_GET['tag'] : '1');
	$page = max((int)$tag, 1);
	echo wfu_uploadedfiles_manager($page);
}

function wfu_uploadedfiles_manager($page = 1, $only_table_rows = false) {
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";
	$table_name3 = $wpdb->prefix . "wfu_dbxqueue";

	if ( !current_user_can( 'manage_options' ) ) return;

	$siteurl = site_url();
	$maxrows = (int)WFU_VAR("WFU_UPLOADEDFILES_TABLE_MAXROWS");

	//get log data from database
	$files_total = $wpdb->get_var('SELECT COUNT(idlog) FROM '.$table_name1.' WHERE action = \'upload\'');
	$filerecs = $wpdb->get_results('SELECT * FROM '.$table_name1.' WHERE action = \'upload\' ORDER BY date_from DESC'.( $maxrows > 0 ? ' LIMIT '.$maxrows.' OFFSET '.(($page - 1) * $maxrows) : '' ));

	//get last record already read
	$last_idlog = get_option( "wordpress_file_upload_last_idlog", array( "pre" => 0, "post" => 0, "time" => 0 ) );
	
	//prepare html
	$echo_str = "";
	if ( !$only_table_rows ) {
		//Update last_idlog option so that next time Uploaded Files menu item is
		//pressed files have been read.
		//Option last_idlog requires a minimum interval of some seconds, defined
		//by advanced variable WFU_UPLOADEDFILES_RESET_TIME, before it can be
		//updated. This way, if the admin presses Uploaded Files menu item two
		//times immediately, the same number of unread files will not change.
		//It is noted that last_idlog option uses two values, 'pre' and 'post'.
		//The way they are updated makes sure that the number of unread files
		//gets reset only when Uploaded Files menu item is pressed and not
		//when the admin browses through the pages of the list (when pagination
		//is activated).
		$limit = (int)WFU_VAR("WFU_UPLOADEDFILES_RESET_TIME");
		if ( $limit == -1 || time() > $last_idlog["time"] + $limit ) {
			$last_idlog["pre"] = $last_idlog["post"];
			$last_idlog["post"] = $wpdb->get_var('SELECT MAX(idlog) FROM '.$table_name1);
			$last_idlog["time"] = time();
			update_option( "wordpress_file_upload_last_idlog", $last_idlog );		
		}
		
		$echo_str .= "\n".'<div class="wrap">';
		$echo_str .= "\n\t".'<h2>List of Uploaded Files</h2>';
		$echo_str .= "\n\t".'<div style="position:relative;">';
		$echo_str .= wfu_add_loading_overlay("\n\t\t", "uploadedfiles");
		$echo_str .= "\n\t\t".'<div class="wfu_uploadedfiles_header" style="width: 100%;">';
		if ( $maxrows > 0 ) {
			$pages = ceil($files_total / $maxrows);
			$echo_str .= wfu_add_pagination_header("\n\t\t\t", "uploadedfiles", $page, $pages);
		}
		$echo_str .= "\n\t\t".'</div>';
		$echo_str .= "\n\t\t".'<table id="wfu_uploadedfiles_table" class="wfu-uploadedfiles wp-list-table widefat fixed striped">';
		$echo_str .= "\n\t\t\t".'<thead>';
		$echo_str .= "\n\t\t\t\t".'<tr>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="5%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>#</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="30%" class="manage-column column-primary">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>File</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="10%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Upload Date</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="10%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>User</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="10%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Properties</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="25%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Remarks</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="10%" class="manage-column">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Actions</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
		$echo_str .= "\n\t\t\t".'</thead>';
		$echo_str .= "\n\t\t\t".'<tbody>';
	}
	//echo the number of unread uploaded files in order to update the
	//notification bubble of the toplevel menu item
	$unread_files_count = wfu_get_unread_files_count($last_idlog["pre"]);
	$echo_str .= "\n\t\t\t".'<!-- wfu_uploadedfiles_unread['.$unread_files_count.'] -->';
	
	$i = ($page - 1) * $maxrows;
	$abspath_notrailing_slash = substr(wfu_abspath(), 0, -1);
	$pagecode = wfu_safe_store_browser_params('wfu_uploaded_files&tag='.$page);
	$nopagecode = wfu_safe_store_browser_params('no_referer');
	foreach ( $filerecs as $filerec ) {
		$i ++;
		$initialrec = $filerec;
		//get all newer associated file records
		$filerecs = wfu_get_rec_new_history($initialrec->idlog);
		//get the latest record of this upload
		$filerec = $filerecs[count($filerecs) - 1];
		$filedata = wfu_get_filedata_from_rec($filerec, false, true, false);
		if ( $filedata == null ) $filedata = array();

		$echo_str .= "\n\t\t\t\t".'<tr class="wfu_row-'.$i.( $initialrec->idlog > $last_idlog["pre"] ? ' wfu_unread' : '' ).'">';
		$file_relpath = ( substr($filerec->filepath, 0, 4) == 'abs:' ? substr($filerec->filepath, 4) : $filerec->filepath );
		$file_abspath = wfu_path_rel2abs($filerec->filepath);
		$displayed_data = array(
			"file"			=> $file_relpath,
			"date"			=> get_date_from_gmt($initialrec->date_from),
			"user"			=> wfu_get_username_by_id($filerec->uploaduserid),
			"properties"	=> '',
			"remarks"		=> '<div class="wfu-remarks-container"></div>',
			"actions"		=> ''
		);
		$properties = wfu_init_uploadedfiles_properties();
		$actions = wfu_init_uploadedfiles_actions();
		$remarks = '';
		//check if file is stored in FTP location
		$file_in_ftp = ( substr($file_abspath, 0, 6) == 'ftp://' || substr($file_abspath, 0, 7) == 'ftps://' || substr($file_abspath, 0, 7) == 'sftp://' );
		//check if file resides inside WP root
		$file_in_root = ( !$file_in_ftp && substr($file_abspath, 0, strlen($abspath_notrailing_slash)) == $abspath_notrailing_slash );
		//check if file exists for non-ftp uploads
		$file_exists = ( $file_in_ftp ? true : file_exists($file_abspath) );
		//check if record is obsolete
		$obsolete = ( $filerec->date_to != "0000-00-00 00:00:00" );
		//check if file is associated with Media item
		$has_media = ( $file_in_root && $file_exists && !$obsolete && isset($filedata["media"]) );
		
		//update properties
		$properties['status']['icon'] = ( $file_exists ? ( $obsolete ? "obsolete" : "ok" ) : "notexists" );
		$properties['userdata']['visible'] = ( count(wfu_get_userdata_from_rec($filerec)) > 0 );
		if ( $has_media ) {
			$properties['media']['visible'] = true;
			$properties['media']['remarks'] = 'File is associated with Media item ID <strong>'.$filedata["media"]["attach_id"].'</strong>';
		}
		$properties['ftp']['visible'] = $file_in_ftp;

		//update actions
		$details_href_net = $siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_details&file=byID:'.$filerec->idlog;
		$actions['details']['href'] = $details_href_net.'&invoker='.$nopagecode;
		if ( WFU_VAR("WFU_UPLOADEDFILES_DEFACTION") == "details" )
			$displayed_data["file"] = '<a href="'.$details_href_net.'&invoker='.$pagecode.'" title="Go to file details">'.$file_relpath.'</a>';
		if ( $has_media ) {
			$actions['media']['visible'] = true;
			$actions['media']['href'] = get_attachment_link( $filedata["media"]["attach_id"] );
		}
		if ( $file_in_root && $file_exists && !$obsolete ) {
			$only_path = wfu_basedir($file_relpath);
			$dir_code = wfu_safe_store_filepath($only_path.'{{'.wfu_basename($file_relpath).'}}');
			$filelink = $siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_browser&dir='.$dir_code;
			$actions['adminbrowser']['visible'] = true;
			$actions['adminbrowser']['href'] = $filelink;
			$actions['download']['visible'] = true;
			$actions['download']['href'] = $filelink;
			if ( WFU_VAR("WFU_UPLOADEDFILES_DEFACTION") == "adminbrowser" )
				$displayed_data["file"] = '<a href="'.$filelink.'" title="Open file in File Browser">'.$file_relpath.'</a>';
		}
		$historylog_href = $siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=view_log&invoker='.$initialrec->idlog;
		$actions['historylog']['href'] = $historylog_href;
		if ( WFU_VAR("WFU_UPLOADEDFILES_DEFACTION") == "historylog" )
			$displayed_data["file"] = '<a href="'.$historylog_href.'" title="Go to View Log record of file">'.$file_relpath.'</a>';
		if ( ( $file_in_ftp || $file_in_root ) && $file_exists && !$obsolete ) {
			$actions['link']['visible'] = true;
			$filelink = $file_relpath;
			if ( $file_in_root ) $filelink = site_url().( substr($filelink, 0, 1) == '/' ? '' : '/' ).$filelink;
			$actions['link']['href'] = $filelink;
			if ( WFU_VAR("WFU_UPLOADEDFILES_DEFACTION") == "link" )
				$displayed_data["file"] = '<a href="'.$filelink.'" title="Open file link">'.$file_relpath.'</a>';
		}

		$displayed_data["properties"] = wfu_render_uploadedfiles_properties($properties, $i);
		$displayed_data["actions"] = wfu_render_uploadedfiles_actions($actions);
		$echo_str .= "\n\t\t\t\t\t".'<th style="word-wrap: break-word;">'.$i.'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<td class="column-primary" data-colname="File">'.$displayed_data["file"];
		$echo_str .= "\n\t\t\t\t\t\t".'<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>';
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Upload Date">'.$displayed_data["date"].'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="User">'.$displayed_data["user"].'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Properties">'.$displayed_data["properties"].'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Remarks">'.$displayed_data["remarks"].'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td data-colname="Actions">'.$displayed_data["actions"].'</td>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
	}
	if ( !$only_table_rows ) {
		$echo_str .= "\n\t\t\t".'</tbody>';
		$echo_str .= "\n\t\t".'</table>';
		$echo_str .= "\n\t".'</div>';
		$handler = 'function() { wfu_attach_uploadedfiles_events(); }';
		$echo_str .= "\n\t".'<script type="text/javascript">if(window.addEventListener) { window.addEventListener("load", '.$handler.', false); } else if(window.attachEvent) { window.attachEvent("onload", '.$handler.'); } else { window["onload"] = '.$handler.'; }</script>';
		$echo_str .= "\n".'</div>';
	}

	return $echo_str;
}

function wfu_init_uploadedfiles_properties() {
	$props["status"] = array(
		"icon"			=> "obsolete",
		"icon-list"		=> array(
			"ok"			=> "dashicons-yes",
			"notexists"		=> "dashicons-trash",
			"obsolete"		=> "dashicons-warning"
		),
		"title"			=> "",
		"title-list"	=> array(
			"ok"			=> "File is Ok",
			"notexists"		=> "File does not exist",
			"obsolete"		=> "Record is invalid"
		),
		"visible"		=> true,
		"remarks"		=> '',
		"remarks-list"	=> array(
			"ok"			=> "File uploaded successfully to the website",
			"notexists"		=> "File does not exist anymore in the website",
			"obsolete"		=> "Record is not valid anymore"
		),
		"code"		=> wfu_create_random_string(6)
	);
	$props["userdata"] = array(
		"icon"		=> "dashicons-id-alt",
		"title"		=> "File has user data",
		"visible"	=> false,
		"remarks"	=> 'File has user data, accessible in File Details',
		"code"		=> wfu_create_random_string(6)
	);
	$props["media"] = array(
		"icon"		=> "dashicons-admin-media",
		"title"		=> "File is associated with Media item",
		"visible"	=> false,
		"remarks"	=> 'File is associated with Media item',
		"code"		=> wfu_create_random_string(6)
	);
	$props["ftp"] = array(
		"icon"		=> "wfu-dashicons-ftp",
		"title"		=> "File saved in FTP",
		"visible"	=> false,
		"remarks"	=> 'File has been saved in FTP location',
		"code"		=> wfu_create_random_string(6)
	);
	
	return $props;
}

function wfu_init_uploadedfiles_actions() {
	$actions["details"] = array(
		"icon"		=> "dashicons-info",
		"title"		=> "View file details",
		"visible"	=> true,
		"href"		=> ""
	);
	$actions["media"] = array(
		"icon"		=> "wfu-dashicons-media-external",
		"title"		=> "Open associated Media item",
		"visible"	=> false,
		"href"		=> ""
	);
	$actions["adminbrowser"] = array(
		"icon"		=> "dashicons-portfolio",
		"title"		=> "Locate file in File Browser",
		"visible"	=> false,
		"href"		=> ""
	);
	$actions["historylog"] = array(
		"icon"		=> "dashicons-backup",
		"title"		=> "Locate file record in View Log",
		"visible"	=> true,
		"href"		=> ""
	);
	$actions["link"] = array(
		"icon"		=> "dashicons-external",
		"title"		=> "Open file link",
		"visible"	=> false,
		"href"		=> ""
	);
	$actions["download"] = array(
		"icon"		=> "dashicons-download",
		"title"		=> "Download file",
		"visible"	=> false,
		"href"		=> ""
	);
	
	return $actions;
}

function wfu_render_uploadedfiles_properties($props, $index) {
	$i = 0;
	$echo_str = "";
	foreach ( $props as $key => $prop ) {
		$ii = $i + 1;
		$iconclass = $prop['icon'];
		if ( isset($prop['icon-list']) ) $iconclass = $prop['icon-list'][$prop['icon']];
		$title = $prop['title'];
		if ( isset($prop['title-list']) ) $title = $prop['title-list'][$prop['icon']];
		$remarks = $prop['remarks'];
		if ( isset($prop['remarks-list']) ) $remarks = $prop['remarks-list'][$prop['icon']];
		$echo_str .= '<div id="p_'.$index.'_'.$ii.'" class="wfu-properties dashicons '.$iconclass.( $i == 0 ? '' : ' wfu-dashicons-after' ).( $prop['visible'] ? '' : ' wfu-dashicons-hidden' ).'" title="'.$title.'"><input type="hidden" class="wfu-remarks" value="'.wfu_plugin_encode_string($remarks).'" /></div>';
		$i ++;
	}
	
	return $echo_str;
}

function wfu_render_uploadedfiles_actions($actions) {
	$i = 0;
	$echo_str = "";
	foreach ( $actions as $key => $action ) {
		$iconclass = $action['icon'];
		if ( isset($action['icon-list']) ) $iconclass = $action['icon-list'][$action['icon']];
		$title = $action['title'];
		if ( isset($action['title-list']) ) $title = $action['title-list'][$action['icon']];
		$echo_str .= '<a class="dashicons '.$iconclass.( $i == 0 ? '' : ' wfu-dashicons-after' ).( $action['visible'] ? '' : ' wfu-dashicons-hidden' ).'" href="'.$action['href'].'" target="_blank" title="'.$title.'"></a>';
		$i ++;
	}
	
	return $echo_str;
}

?>
