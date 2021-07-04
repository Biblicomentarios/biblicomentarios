<?php

function wpdf_editor_ajax_action_function() {

	$modules = $_POST["modules"];

	if(get_magic_quotes_gpc()) {
		$keyword = stripslashes(sanitize_text_field($_POST['keyword']));
	} else {
		$keyword = sanitize_text_field($_POST["keyword"]);
	}

	if (!wp_verify_nonce($_POST["wpnonce"], 'wpdf_security_nonce')) {
		echo json_encode(array("error" => "Invalid request."));
		exit;
	}	

	if(empty($modules) || !is_array($modules)) {
		echo json_encode(array("error" => "No content source found."));
		exit;	
	}
	
	if(empty($keyword)) {
		echo json_encode(array("error" => "Keyword is empty."));
		exit;	
	}

	global $source_infos, $modulearray;
	@require_once("api.class.php");	
	
	$options = get_option("wpinject_settings");
	$items_per_req = $options["general"]["options"]["items_per_req"]["value"];
	if(empty($items_per_req)) {$items_per_req = 30;}
	
	$marray = array();
	foreach($modules as $module) {
		$mn = sanitize_text_field($module["name"]);
		$modulerun = sanitize_text_field($module["module_run"]);
		$start = 1 + (($modulerun - 1) * $items_per_req);

		$marray[$mn] = array("count" => $items_per_req, "start" => $start);
	}
	
	$api = new wpdf_API_request;
	//$result = $api->api_content_bulk($keyword, array($module => array("count" => $items_per_req, "start" => $start))); 
	$result = $api->api_content_bulk($keyword, $marray); 
	
	if(is_array($result)) {
		foreach($modules as $module) {
			$mn = sanitize_text_field($module["name"]);
			$result[$mn]["modulerun"] = sanitize_text_field($module["module_run"]);
		}	
		echo json_encode(array("result" => $result));
		exit;	
	} else {
		echo json_encode(array("error" => "Content search failed"));
		exit;		
	}
}
/*
function wpdf_editor_ajax_set_featured_function() {

	$src = $_POST["src"];
	$post_id = $_POST["post_id"];

	$nonce = $_POST["wpnonce"];
	if (!wp_verify_nonce($nonce, 'wpdf_security_nonce')) {
		echo json_encode(array("error" => "Invalid request."));
		exit;
	}	

	if(empty($src)) {
		echo json_encode(array("error" => "No image source found."));
		exit;	
	}
	
	if(empty($post_id)) {
		echo json_encode(array("error" => "No post found. This feature requires that an auto-save or draft of the current post was saved first."));
		exit;	
	}

	$result = media_sideload_image($src, $post_id);
	$attachments = get_posts(array('numberposts' => '1', 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'post_date', 'order' => 'DESC'));

	if(sizeof($attachments) > 0){
		set_post_thumbnail($post_id, $attachments[0]->ID);	
	}
	$newsrc = wp_get_attachment_image_src( $attachments[0]->ID, "full" );
	
	if(empty($newsrc)) {
		echo json_encode(array("error" => "Image could not be saved."));
		exit;	
	} else {
		echo json_encode(array("result" => $newsrc[0]));
		exit;		
	}	
}

function wpdf_editor_ajax_save_keys_function() {

	$flickrapi = $_POST["flickrapi"];
	
	$nonce = $_POST["wpnonce"];
	if (!wp_verify_nonce($nonce, 'wpdf_security_nonce')) {
		echo json_encode(array("error" => "Invalid request."));
		exit;
	}

	$options = get_option("wpinject_settings");
	$options["flickr"]["options"]["appid"]["value"] = $flickrapi;
	update_option("wpinject_settings", $options);

	echo json_encode(array("success" => "true"));
	exit;	
}
*/

function wpdf_save_image_alt($src, $post_id, $thumb) {

	$result = media_sideload_image($src, $post_id);
	$attachments = get_posts(array('numberposts' => '1', 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'post_date', 'order' => 'DESC'));

	if(sizeof($attachments) > 0 && $thumb){
		set_post_thumbnail($post_id, $attachments[0]->ID);	
	}	
	
	$newsrc = wp_get_attachment_image_src( $attachments[0]->ID, "full" );

	if(is_array($newsrc) && !empty($newsrc[0])) {
		return $newsrc[0];
	} else {
		return false;	
	}	
}

function wpdf_save_image($url, $post_id, $thumb = 0, $filename = "", $keyword = "", $attr = "") {

    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    $tmp = download_url( $url );  // Download file to temp location, returns full server path to temp file, ex; /home/user/public_html/mysite/wp-content/26192277_640.tmp

    if ( is_wp_error( $tmp ) ) {
		@unlink($file_array['tmp_name']);   // clean up
		$file_array['tmp_name'] = '';	
		
		$retry = wpdf_save_image_alt($url, $post_id, $thumb);
		if($retry == false) {
			$error_string = $tmp->get_error_message();
			return array("error" => $error_string);	
		} else {
			return $newsrc;
		}
    }

    preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $url, $matches);    // fix file filename for query strings
    $url_filename = basename($matches[0]);                                                  // extract filename from url for title
    $url_type = wp_check_filetype($url_filename);                                           // determine file type (ext and mime/type)
	
    if (!empty($filename)){    // override filename if given, reconstruct server path
	
		$filename = str_replace("{filename}", pathinfo($url_filename, PATHINFO_FILENAME), $filename);
		$filename = str_replace("{keyword}", $keyword, $filename);
		$filename = str_replace("{rand}", rand(100000, 999999), $filename);
		$filename = str_replace("{timestamp}", time(), $filename);
		$filename = str_replace("{date}", date("Y-m-d"), $filename);
	
        $filename = sanitize_file_name($filename);
        $tmppath = pathinfo( $tmp );                                                        // extract path parts
        $new = $tmppath['dirname'] . "/". $filename . "." . $tmppath['extension'];          // build new path
        rename($tmp, $new);                                                                 // renames temp file on server
        $tmp = $new;                                                                        // push new filename (in path) to be used in file array later
    }

    $file_array['tmp_name'] = $tmp;                                                         // full server path to temp file

    if (!empty($filename)) {
        $file_array['name'] = $filename . "." . $url_type['ext'];                           // user given filename for title, add original URL extension
    } else {
        $file_array['name'] = $url_filename;                                                // just use original URL filename
    }

    if ( empty( $post_data['post_parent'] ) ) {
        $post_data['post_parent'] = $post_id;
    }

    if(!empty($attr)) {
	$post_data['post_excerpt'] = $attr;
    }
    
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $att_id = media_handle_sideload( $file_array, $post_id, null, $post_data );             // $post_data can override the items saved to wp_posts table, like post_mime_type, guid, post_parent, post_title, post_content, post_status

    if ( is_wp_error($att_id) ) {
		$error_string = $att_id->get_error_message();
        @unlink($file_array['tmp_name']);   // clean up
		return array("error" => $error_string);		
    }

    if ($thumb) {
        set_post_thumbnail($post_id, $att_id);			
    }
	
	$newsrc = wp_get_attachment_image_src( $att_id, "full" );
	
	if ( is_wp_error($newsrc) ) {
		$error_string = $newsrc->get_error_message();
		return array("error" => $error_string);		
	} elseif(is_array($newsrc) && !empty($newsrc[0])) {
		return $newsrc[0];
	} else {
		return array("error" => "Image could not be saved to server.");		
	}
}

function wpdf_save_image_function() {

	$url = esc_url($_POST["src"]);
	$post_id = sanitize_text_field($_POST["post_id"]);
	$thumb = sanitize_text_field($_POST["feat_img"]);
	$filename = sanitize_text_field($_POST["filename"]);
	$keyword = sanitize_text_field($_POST["keyword"]);
	$attr = sanitize_text_field($_POST["attr"]);
	
	$nonce = $_POST["wpnonce"];
	if (!wp_verify_nonce($nonce, 'wpdf_security_nonce')) {
		echo json_encode(array("error" => "Invalid request."));
		exit;
	}		
	
	if(empty($url)) {
		echo json_encode(array("error" => "No image source found."));
		exit;	
	}
	
	if(empty($post_id)) {
		echo json_encode(array("error" => "No post found. This feature requires that an auto-save or draft of the current post was saved first."));
		exit;	
	}

	$newsrc = wpdf_save_image($url, $post_id, $thumb, $filename, $keyword, $attr);

	if(is_array($newsrc)) {
		echo json_encode(array("error" => $newsrc["error"]));
		exit;		
	} else {
		echo json_encode(array("result" => $newsrc));
		exit;	
	}
}

function wpdf_save_multiple_images_function() {

	$images = $_POST["images"];
	$post_id = sanitize_text_field($_POST["post_id"]);
	$thumb = sanitize_text_field($_POST["feat_img"]);
	$filename = sanitize_text_field($_POST["filename"]);
	$keyword = sanitize_text_field($_POST["keyword"]);
	
	$nonce = $_POST["wpnonce"];
	if (!wp_verify_nonce($nonce, 'wpdf_security_nonce')) {
		echo json_encode(array("error" => "Invalid request."));
		exit;
	}		
	
	if(empty($images) || !is_array($images)) {
		echo json_encode(array("error" => "No image source found."));
		exit;	
	}
	
	if(empty($post_id)) {
		echo json_encode(array("error" => "No post found. This feature requires that an auto-save or draft of the current post was saved first."));
		exit;	
	}
	
	$newimages = array();

	foreach($images as $url) {
	
		$url = esc_url($url);
	
		$newsrc = wpdf_save_image($url, $post_id, $thumb, $filename, $keyword, $attr);

		if(is_array($newsrc)) {
			echo json_encode(array("error" => $newsrc["error"]));
			exit;		
		} else {
			$newimages[] = $newsrc;
		}	
	}
	
	if(empty($newimages)) {
		echo json_encode(array("error" => "Images could not be saved to the server."));
		exit;		
	} else {
		echo json_encode(array("result" => $newimages));
		exit;	
	}		
}
?>