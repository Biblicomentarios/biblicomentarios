<?php
/**
 Plugin Name: ImageInject
 Plugin URI: http://wpscoop.com/wp-inject/
 Version: 1.17
 Description: Insert photos into your posts or set a featured image in less than a minute! ImageInject allows you to search the huge Flickr image database for creative commons photos directly from within your WordPress editor. Find great photos related to any topic and inject them into your post. Previously known as WP Inject.
 Author: Thomas Hoefter
 Author URI: http://wpscoop.com/
*/

include_once("info_sources_options.php");

function wpdf_add_menu_pages() {
	$wpdf_settings = add_options_page('Image Inject', 'Image Inject', 'manage_options', 'wpdf-options', 'wpdf_settings_page');
	add_action( "admin_print_scripts-$wpdf_settings", 'wpdf_settings_page_scripts' );		
}
add_action('admin_menu', 'wpdf_add_menu_pages');

function wpdf_activate_blog() {
	include("info_sources_options.php");

	$wpinject_settings = $modulearray;
	foreach($wpinject_settings as $module => $moduledata) {
		if($moduledata["enabled"] != 2 && $moduledata["enabled"] != 1) {
			unset($wpinject_settings[$module]["options"]);
			unset($wpinject_settings[$module]["templates"]);
		}
	}
	
	update_option('wpinject_settings',$wpinject_settings);		
}

function wpdf_activate($network_wide) {
	if ( is_multisite() && $network_wide ) {
		global $wpdb;
 
		$current_blog = $wpdb->blogid;

		$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		foreach ($blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			wpdf_activate_blog();
		}
 
		switch_to_blog($current_blog);
	} else {
		wpdf_activate_blog();
	}
}
register_activation_hook(__FILE__, 'wpdf_activate');


function wpdf_activate_new_blog($blog_id) {
    global $wpdb;
 
    if (is_plugin_active_for_network('wp-inject/wpinject.php')) {
        $old_blog = $wpdb->blogid;
        switch_to_blog($blog_id);
        wpdf_activate_blog();
        switch_to_blog($old_blog);
    }
}
add_action( 'wpmu_new_blog', 'wpdf_activate_new_blog', 10, 6);       

function wpdf_deactivate($network_wide) {
	if ( is_multisite() && $network_wide ) {
		global $wpdb;
 
		$current_blog = $wpdb->blogid;

		$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		foreach ($blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			delete_option('wpinject_settings');	
		}
 
		switch_to_blog($current_blog);
	} else {
		delete_option('wpinject_settings');	
	}	
}
register_deactivation_hook( __FILE__, 'wpdf_deactivate' );

///////////////////////// SETTINGS PAGE

function wpdf_settings_page_scripts() {
	wp_enqueue_script('jquery');
	$wpi_url = WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)); //plugins_url( 'WPInject/wpdf-editor-styles.css' )
	
	wp_register_style( 'wpinject-editor-css', $wpi_url . '/wpdf-editor-styles.css' );
	wp_enqueue_style( 'wpinject-editor-css' );		
}

/*
if(isset($_GET['page']) && $_GET['page'] == 'wpdf-options' ) {
	//add_action('admin_head', 'wpdf_settings_page_head');		
}
function wpdf_settings_page_head() {
	?>
    <script type="text/javascript">	
	jQuery(document).ready(function($) {
		var index;
		var modules = ["flickr"];
		for (index = 0; index < modules.length; ++index) {
			toggle("#" + modules[index], "#" + modules[index] + "_enabled");
		}
	});
	
	function toggle(className, obj) {
		var jQueryinput = jQuery(obj);
		if (jQueryinput.prop('checked')) jQuery(className).show();
		else jQuery(className).hide();
	}
	</script>		
	<?php
}*/

function wpdf_settings_page() {
	global $source_infos, $modulearray;

	$options = $modulearray;
	$optionsarray = get_option("wpinject_settings");

	if($_GET["test"] == 1) {	
		@require_once("api.class.php");	
		$api = new wpdf_API_request;
		$result = $api->api_content_bulk("fun", array("pixabay" => array("count" => 120, "start" => 1))); 
		//print_r($result);
	}
	
	if($_POST["save_options"]) {
		check_admin_referer( 'imageinject_save_options' );
		
		foreach($options as $module => $moduledata) {

			if($optionsarray[$module]["enabled"] != 2) {
				$optionsarray[$module]["enabled"] = sanitize_text_field($_POST[$module."_enabled"]);
				if(empty($_POST[$module."_enabled"])) {$optionsarray[$module]["enabled"] = 0;}
				
				if($optionsarray[$module]["enabled"] == 1 && empty($optionsarray[$module]["options"])) {
					$optionsarray[$module] = $options[$module];
					$optionsarray[$module]["enabled"] = 1;
				}
			}
			
			if($optionsarray[$module]["enabled"] == 1 || $optionsarray[$module]["enabled"] == 2) {
				foreach($moduledata["options"] as $option => $data) {	
				
					if($option == "img_template" || $option == "attr_template" || $option == "attr_template_multi") {
					
						$_POST[$module."_".$option] = stripslashes($_POST[$module."_".$option]);
						if($option == "attr_template" && (strpos($_POST[$module."_".$option], "{link}") === false || strpos($_POST[$module."_".$option], "{author}") === false)) {
							echo '<div class="error"><p><strong>WARNING: </strong> The Attribution Template setting has to contain the {link} and {author} tag with a proper link back to the owner or you will be <strong>in violation of the license</strong> of Flickr photos you insert!</p></div>';	
						}
					}
				
					$optionsarray[$module]["options"][$option]["value"] = $_POST[$module."_".$option];				
				}		
			}
		}

		$result = update_option("wpinject_settings", $optionsarray);
		if($result) {
			echo '<div class="updated"><p>Options have been updated.</p></div>';	
		} else {
			echo '<div class="error"><p>Error: Options could not be updated.</p></div>';	
		}			
	}	
	
	/*if(!empty($_POST) && empty($_POST["save_options"])) {
		// VERIFICATION FUNCTION
		foreach($options as $module => $moduledata) {
			if($_POST[$module."_verify"]) {
				@require_once("api.class.php");
				$api = new wpdf_API_request;
				$result = $api->api_content_bulk("camera",array($module => 1));
				if(empty($result[$module]["error"]) && isset($result[$module][0]["content"])) {
					if($module == "amazon") {$options[$module]["options"]["public_key"]["verified"] = 1;} else {$optionsarray[$module]["options"]["appid"]["verified"] = 1;}
					update_option("wpinject_settings", $optionsarray);
					echo '<div class="updated"><p>'.$moduledata["name"].' has been verified and is working!</p></div>';					
				} else {
					echo '<div class="error"><p>'.$result[$module]["error"].'</p></div>';	
				}
			}
		}	
	}*/
?>

<div class="wrap">

	<h2><?php _e("ImageInject Settings","wpinject") ?></h2>
	
	<div style="width:28%;float: right;">		
		<div class="wpdf_settings_box">
			<p style="margin-top: 0;">To <strong>insert images</strong> go to the WordPress "<a href="post-new.php">New Post</a>" or "<a href="post-new.php?post_type=page">New Page</a>" screens where you will find the ImageInject metabox to search for great photos!</p>
			
			<p>Please <a href="http://wpscoop.com/wp-inject/#docs" target="_blank"><strong>read my short ImageInject tutorial</strong></a> for more details on all the settings on this page and what exactly they do.</p>
		
			<p>Having problems or found a bug? Please <a href="http://wpscoop.com/contact" target="_blank">contact me</a> or post in the WordPress support forum.</p>

		</div>	
		
		<div class="wpdf_settings_box">		
			<p style="text-align: center;margin: 0;">If you find ImageInject useful <strong>please share!</strong><br/>
				<a title="Share ImageInject on Twitter" target="_blank" class="wpdf_share_twitter" href="https://twitter.com/home?status=This%20%23WordPress%20plugin%20by%20@wpscoop%20makes%20finding%20and%20inserting%20free%20photos%20into%20my%20blog%20posts%20super%20easy:%20http://wpscoop.com/wp-inject"></a>
				<a title="Share ImageInject on Facebook" target="_blank" class="wpdf_share_fb" href="https://www.facebook.com/sharer/sharer.php?u=http://wpscoop.com/wp-inject/"></a>
				<a title="Share ImageInject on Google+" target="_blank" class="wpdf_share_google" href="https://plus.google.com/share?url=http://wpscoop.com/wp-inject/"></a>
			</p>		
		</div>				
	
		<div class="wpdf_settings_box">
			<p style="margin-top: 0;"><strong>Multiple WordPress Sites? No Problem!</strong></p><p>Discover <a href="http://cmscommander.com/" target="_blank">CMS Commander</a> - the best way to manage multiple WordPress sites from a single dashboard. Try it for free now and see how much time you can save!
		</div>	
	
		<div class="wpdf_settings_box">
			<a title="Visit WPscoop!" target="_blank" href="http://wpscoop.com/"><img src="<?php echo plugins_url( '', __FILE__ ); ?>/images/WPscoop-logo.png" alt="WPscoop logo" /></a>	
			<p>Check out <a target="_blank" title="Visit WPscoop!" href="http://wpscoop.com/"><strong>WPscoop.com</strong></a> where I blog about WordPress and publish my free plugins.</p>
		
		</div>		
		
		<div class="wpdf_settings_box">
			<p><strong>Image Sources in ImageInject</strong><br>Click for more information:</p>
			
			<p><a href="http://flickr.com/" target="_blank"><strong>Flickr</strong></a>, the popular photo uploading service by Yahoo, contains more than 200 million creative commons images.</p>
			
			<p><a href="http://pixabay.com/" target="_blank"><strong>Pixabay</strong></a> is a directory for high quality public domain images. They offer more than 150,000 great photos for you to use.</p>
		</div>
	</div>	
	
	<form method="post" name="wpdf_options">

	<?php wp_nonce_field( "imageinject_save_options", '_wpnonce', false ) ?>
	
	<div style="width:71%;">

	<p class="submit"><input class="button-primary" type="submit" name="save_options" value="<?php _e("Save All Settings","wpinject") ?>" /></p>		

	<?php $num = 0; foreach($options as $module => $moduledata) { $num++; ?>

		<?php if($moduledata["enabled"] == 2) { ?>
		<h3><?php echo $moduledata["name"]; ?></h3>
		<?php } else { ?>
		<h3><input checked style="margin-right: 5px; margin-top: -2px;display:none;" onclick="toggle('#<?php echo $module; ?>', this)" class="button" type="checkbox" id="<?php echo $module."_enabled"; ?>" name="<?php echo $module."_enabled"; ?>" value="1" <?php if(1 == $optionsarray[$module]["enabled"]) {echo "checked";} ?>/><label for="<?php echo $module."_enabled"; ?>"><?php echo $moduledata["name"]; ?> <?php _e("Settings","wpinject") ?></label></h3>
		<?php } ?>
		
		<div id="<?php echo $module; ?>">	

		<?php if(empty($moduledata["options"])) { ?>
			<p><?php _e("No settings required for this content source. To edit its templates go to the Templates page.","wpinject"); ?></p>
		<?php } else { ?>
		<table class="form-table" style="clear: none !important;">
			<tbody>				
		
				<?php foreach($moduledata["options"] as $option => $data) {
					if($option != "flickr_appid" && $option != "title" && $option != "unique" && $option != "error" && $option != "unique_direct" && $option != "title_direct") {
					
						if(!empty($optionsarray[$module]["options"][$option]["value"])) {
							$value = $optionsarray[$module]["options"][$option]["value"];
						} else {
							$value = $data["value"];
						}
						
						if($data["type"] == "checkbox" && empty($optionsarray[$module]["options"][$option]["value"]) && !empty($optionsarray[$module]["options"])) {
							$value = "";
						}
					
						if($data["type"] == "text") { // Text Option 
							if($data["display"] == "none") {$dnon = 'style = "display: none;"';} else {$dnon = "";}
						?> 
							<tr <?php echo $dnon;?>>
								<th scope="row"><label for="<?php echo $module."_".$option;?>"><?php echo $data["name"];?></label></th>
								<td><input class="regular-text" type="text" name="<?php echo $module."_".$option;?>" value="<?php echo $value; ?>" />
									<!-- VERIFICATION BUTTON DISPLAY -->
									<?php if($optionsarray[$module]["options"][$option]["verified"] === 0) {?>
										<input class="button" type="submit" name="<?php echo $module."_verify";?>" value="<?php _e("Verify","wpinject"); ?>" <?php if(empty($value)) {echo "disabled";} ?> />
										<?php if(!empty($source_infos["sources"][$module]["signup"])) {?><a href="<?php echo $source_infos["sources"][$module]["signup"]; ?>" target="_blank">Sign Up</a><?php } ?>
									<?php } elseif($optionsarray[$module]["options"][$option]["verified"] === 1) {?>
										<?php echo '<img style="margin-bottom: -3px;" src="'.WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)).'/images/check.png" /> Verified'; ?>
									<?php } ?>

								</td>	
							</tr>
						<?php } elseif($data["type"] == "select") { // Select Option ?>
							<tr>	
								<th scope="row"><label for="<?php echo $module."_".$option;?>"><?php echo $data["name"];?></label></th>
								<td><select name="<?php echo $module."_".$option;?>">
									<?php foreach($data["values"] as $val => $name) { ?>
									<option value="<?php echo $val;?>" <?php if($val == $value) {echo "selected";} ?>><?php echo $name; ?></option>
									<?php } ?>		
								</select></td>	
							</tr>
						<?php } elseif($data["type"] == "checkbox") { // checkbox Option ?>		
							<tr>	
								<th scope="row"><label for="<?php echo $module."_".$option;?>"><?php echo $data["name"];?></label></th>
								<td><input class="button" type="checkbox" id="<?php echo $module."_".$option; ?>_s" name="<?php echo $module."_".$option; ?>" value="1" <?php if(1 == $value) {echo "checked";} ?>/> <label for="<?php echo $module."_".$option;?>_s" style="padding-top: 7px;"><?php echo $data["info"]; ?><label>

								</td>	
							</tr>									
						<?php } elseif($data["type"] == "textarea") { // textarea Option ?>		
							<tr>	
								<th scope="row"><label for="<?php echo $module."_".$option;?>"><?php echo $data["name"];?></label></th>
								
								<td>
								<textarea cols="60" rows="1" name="<?php echo $module."_".$option; ?>"><?php echo $value; ?></textarea>
								</td>	
							</tr>									
						<?php } ?>	
						
					<?php } ?>
				<?php } ?>
		
			</tbody>
		</table>					
		<?php } ?>
		</div>	
	<?php } ?>
	

	</div>
	
	<p class="submit"><input class="button-primary" type="submit" name="save_options" value="<?php _e("Save All Settings","wpinject") ?>" /></p>	

	</form>	
	
	<h3>Available Template Tags</h3>
	<p>You can use the following tags in the "<strong>Image Template</strong>" setting field:</p>
	<p>
		<strong>{keyword}</strong> - The keyword you searched for with ImageInject.<br/>
		<strong>{yoast-keyword}</strong> - Inserts the "Focus Keyword" as set in the WordPress SEO by Yoast plugin for the post.<br/>	
		<strong>{title}</strong> - The title of the image on Flickr.<br/>
		<strong>{description}</strong> -  The description of the image on Flickr<br/>
		<strong>{author}</strong> - Flickr name or username of the author.<br/>
		<strong>{link}</strong> - Link to the image page on Flickr<br/>
		<strong>{src}</strong> - The image file in the specified size<br/>
	</p>
	<p>The following tags are available in the "<strong>Attribution Template</strong>" field:</p>	
	<p>
		<strong>{keyword}</strong> - The keyword you searched for with ImageInject.<br/>
		<strong>{author}</strong> - Flickr name or username of the author.<br/>
		<strong>{link}</strong> - Link to the image page on Flickr<br/>
		<strong>{cc_icon}</strong> - A small creative commons icon with a link to the license<br/>
		<strong>{license_name}</strong> - The name of the creative commons license the photo uses<br/>
		<strong>{license_link}</strong> - The link to the creative commons license the photo uses<br/>
	</p>
	<p>The following tags are available in the "<strong>Filename Template</strong>" field:</p>	
	<p>
		<strong>{filename}</strong> - The original filename.<br/>
		<strong>{keyword}</strong> - The keyword you searched for.<br/>
		<strong>{timestamp}</strong> - Timestamp of when the image was uploaded to your blog.<br/>
		<strong>{date}</strong> - Date of when the image was uploaded to your blog.<br/>		
		<strong>{rand}</strong> - A random number.<br/>
	</p>	
<?php
}

/////////////////////// META BOX

add_action( 'add_meta_boxes', 'wpdf_editor_metabox' );
function wpdf_editor_metabox() {
	$screens = get_post_types();
	foreach($screens as $screen) {
		add_meta_box('wpdf_editor_section',__( 'ImageInject - Find Free Images', 'wpinject' ), 'wpdf_editor_metabox_content', $screen);
	}
}

function wpdf_editor_metabox_content($post) {
	global $modulearray;

	$optionsa = $modulearray;

	$options = get_option("wpinject_settings");

	$moduleactive = 0;$modulecontent = "";
	if(is_array($options)) {
		foreach($optionsa as $module => $moduledata) {
			if(!empty($options[$module])) {
				$moduledata = $options[$module];
			}
			if($moduledata["enabled"] != 2 && $module != "general" && $module != "advanced") {
				if(!empty($moduledata["options"]["appid"]["value"]) || $module == "pixabay") {$moduleactive = 1;}

				/*if(empty($moduledata["options"]["appid"]["value"]) && $module != "pixabay") {
					$modulecontent .= '<label for="module-'.$module.'"><input type="checkbox" id="module-'.$module.'" name="modules[]" value="'.$module.'" disabled> <a href="options-general.php?page=wpdf-options" title="Clcik to go the the settings page and activate this module.">'.$moduledata["name"].'</a></label><br/>';
				} else*/ if($moduledata["enabled"] == 1) {
					$modulecontent .= '<label for="module-'.$module.'"><input type="checkbox" id="module-'.$module.'" name="modules[]" value="'.$module.'" checked> '.$moduledata["name"].'</label><br/>';
				} else {
					$modulecontent .= '<label for="module-'.$module.'"><input type="checkbox" id="module-'.$module.'" name="modules[]" value="'.$module.'"> '.$moduledata["name"].'</label><br/>';
				}
			}
		}
	} else {
		$moduleactive = -1;
		echo "Error: Options for ImageInject not found. Please try de- and reactivating the plugin.";
	}
	
	if($moduleactive == 0) {
		?>
		<div id="wpdf_save_keys_form">
			<p><?php _e("To start injecting images please enter your Flickr API key below:","wpinject") ?></p>
			<label for="flickr_appid">Flickr API Key: <input type="text" value="" id="flickr_appid" name="flickr_appid" class="regular-text"></label><br/>
			<p><?php _e('For more settings head to the <a href="/wp-admin/options-general.php?page=wpdf-options">ImageInject Options</a> page.',"wpinject") ?></p>
			<p><input type="submit" value="Save" id="wpdf_save_keys" name="wpdf_save_keys" class="button-primary"></p>
		</div>
		<?php
	}
	?>
	
	<div id="wpdf_main" <?php if($moduleactive == 0 || $moduleactive == -1) { echo 'style="display:none;"';} ?>>
	
		<div id="wpdf_modules">
			<div>
				<input placeholder="<?php _e("Enter a keyword to find photos","wpinject") ?>" type="text" value="" size="30" class="newtag form-input-tip" name="wpdf_keyword" id="wpdf_keyword">

				<div class="wpdf_search_item_content">
					<a style="margin-right: 10px;" type="button" class="button wpdf-module" id="wpdf-searchbutton">Search</a>
					
					<div class="wpdf_big_container">
						<div class="wpdf_searchwhat">
							<p><?php echo $modulecontent; ?></p>
						</div>
					</div>

				</div>				

				<a href="#" id="wpdf_get_title"><?php _e("&rarr; Copy Title","wpinject") ?></a>
				<?php if(function_exists("wpseo_init")) { ?>
					<a href="#" id="wpdf_get_seo_keyword"><?php _e("&rarr; Copy SEO Keyword","wpinject") ?></a>
				<?php } ?>
			</div>
		</div>
		
		<div id="wpdf_controls">	
		
			<strong><?php _e("All Selected:","wpinject") ?> </strong>
			<a href="#" title="<?php _e("Insert all selected images","wpinject") ?>" id="wpdf_insert_images_normal"><?php _e("Insert Normal","wpinject") ?></a>
			<a href="#" title="<?php _e("Insert all selected images aligned to the left","wpinject") ?>" id="wpdf_insert_images_left"><?php _e("Align Left","wpinject") ?></a>
			<a href="#" title="<?php _e("Insert all selected images aligned to the right","wpinject") ?>" id="wpdf_insert_images_right"><?php _e("Align Right","wpinject") ?></a>
			<a href="#" title="<?php _e("Insert all selected images aligned to the center","wpinject") ?>" id="wpdf_insert_images_center"><?php _e("Align Center","wpinject") ?></a>
					
			<a href="#" title="<?php _e("Remove all selected images","wpinject") ?>" id="wpdf_remove_selected"><?php _e("Remove","wpinject") ?></a>	

			<div style="float: right;">
				<label for="wpdf_size_sq"><input type="radio" class="wpdf_size_mult" name="wpdf_size" id="wpdf_size_sq" value="square">SQ</label>
				<label for="wpdf_size_s"><input type="radio" class="wpdf_size_mult" name="wpdf_size" id="wpdf_size_s" value="small" checked>S</label>
				<label for="wpdf_size_m"><input type="radio" class="wpdf_size_mult" name="wpdf_size" id="wpdf_size_m" value="medium">M</label>
				<label for="wpdf_size_l"><input type="radio" class="wpdf_size_mult" name="wpdf_size" id="wpdf_size_l" value="large">L</label>
			</div>	
			
		</div>			
	
		<div id="wpdf_message_box">		
		</div>	
		
		<div id="wpdf_results">			
		</div>
		
		<div id="wpdf_share_box">Enjoying ImageInject? <strong>Please share!</strong> 
			<a title="ImageInject tutorial on WPscoop.com" target="_blank" class="wpdf_docs_link" href="http://wpscoop.com/wp-inject">Docs</a>			
			<a title="ImageInject settings page" target="_blank" class="wpdf_settings_link" href="options-general.php?page=wpdf-options">Settings</a>		
			<a title="Share ImageInject on Twitter" target="_blank" class="wpdf_share_twitter" href="https://twitter.com/home?status=This%20%23WordPress%20plugin%20by%20@wpscoop%20makes%20finding%20and%20inserting%20free%20photos%20into%20my%20blog%20posts%20super%20easy:%20http://wpscoop.com/wp-inject"></a>
			<a title="Share ImageInject on Facebook" target="_blank" class="wpdf_share_fb" href="https://www.facebook.com/sharer/sharer.php?u=http://wpscoop.com/wp-inject/"></a>
			<a title="Share ImageInject on Google+" target="_blank" class="wpdf_share_google" href="https://plus.google.com/share?url=http://wpscoop.com/wp-inject/"></a>
		</div>			
		
		<div style="clear: both;"></div>

		<div id="wpdf_ri">	
		
			<div id="wpdf_result_item" class="wpdf_result_item">
			
				<div class="wpdf_result_item_nav">
					<input class="wpdf_select_item_o" type="checkbox" name="wpdf_select_item_o" value="1">
				</div>		
			
				<div class="wpdf_result_item_content">
				</div>	
				
				<div class="wpdf_result_item_save" style="display:none;">
				</div>					

				<div style="clear: both;"></div>
			</div>	

		</div>

	</div>
	<?php
}

// Header
function wpdf_editor_head() {
	global $post;
	
	$options = get_option("wpinject_settings");
?>
    <script type="text/javascript">			
	<?php if($options["general"]["options"]["save_images"]["value"] == 1) { ?>
		var wpdf_save_images = 1;
	<?php } else { ?>
		var wpdf_save_images = 0;
	<?php } ?>
	
	var wpdf_default_align = '<?php echo $options["general"]["options"]["default_align"]["value"]; ?>'; 	
	var wpdf_img_template = '<?php echo $options["advanced"]["options"]["img_template"]["value"]; ?>'; 	
	var wpdf_attr_template = '<?php echo $options["advanced"]["options"]["attr_template"]["value"]; ?>'; 
	var wpdf_attr_template_multi = '<?php echo $options["advanced"]["options"]["attr_template_multi"]["value"]; ?>'; 
	var wpdf_filename_template = '<?php echo $options["advanced"]["options"]["filename_template"]["value"]; ?>'; 
	var wpdf_attr_location = '<?php echo $options["general"]["options"]["attr_location"]["value"]; ?>'; 
	var wpdf_wpi_attr = '<?php echo $options["general"]["options"]["wpi_attr"]["value"]; ?>'; 
	var wpdf_feat_img_size = '<?php echo $options["general"]["options"]["feat_img_size"]["value"]; ?>'; 
	var cur_post_id = <?php echo $post->ID; ?>; 
	var wpdf_plugin_url = '<?php echo plugins_url( '', __FILE__ ); ?>';
	var wpdf_security_nonce = {
		security: '<?php echo wp_create_nonce('wpdf_security_nonce');?>'
	}		
	</script>
<?php
}

function wpdf_editor_scripts() {
	wp_register_style( 'wpinject-editor-css', plugins_url( 'wpdf-editor-styles.css', __FILE__ ) );
	wp_enqueue_style( 'wpinject-editor-css' );	

	wp_register_script( 'wpinject-js', plugins_url( 'wpdf-editor-js.js', __FILE__ ) );
	wp_enqueue_script( 'wpinject-js' );		
}

if(is_admin()){
	if(is_multisite()) {
		if(preg_match("~/wp-admin/post\.php$~", $_SERVER['SCRIPT_NAME']) || preg_match("~/wp-admin/post-new\.php$~", $_SERVER['SCRIPT_NAME'])){
			add_action('admin_head', 'wpdf_editor_head');		
			add_action('admin_enqueue_scripts', 'wpdf_editor_scripts');
		}		
	
		if(preg_match("~/wp-admin/admin-ajax\.php$~", $_SERVER['SCRIPT_NAME'])) {
			require_once('wpdf_ajax.php');
			add_action('wp_ajax_wpdf_editor', 'wpdf_editor_ajax_action_function');
			add_action('wp_ajax_wpdf_save_to_server', 'wpdf_save_image_function');
			add_action('wp_ajax_wpdf_save_multiple_to_server', 'wpdf_save_multiple_images_function');
		}		

	} else {
		global $pagenow;
		if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
			add_action('admin_head', 'wpdf_editor_head');		
			add_action('admin_enqueue_scripts', 'wpdf_editor_scripts');
		}	
		
		if($pagenow == 'admin-ajax.php'){
			require_once('wpdf_ajax.php');
			add_action('wp_ajax_wpdf_editor', 'wpdf_editor_ajax_action_function');
			add_action('wp_ajax_wpdf_save_to_server', 'wpdf_save_image_function');
			add_action('wp_ajax_wpdf_save_multiple_to_server', 'wpdf_save_multiple_images_function');
		}		
	}
}
?>