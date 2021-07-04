<!--<div id="wpa-masthead">
	<img src="<?php echo plugins_url( '/images/wp_assist_tech_plugin_logo.png', __FILE__ ); ?>" alt="WP Assist Website" height="32" width="275" />
</div>-->

<div class="wrap" style="">
	
				
	<div class="metabox-holder">
		
		<form name="SEOAutoLinks" action="<?php echo $action_url;?>" method="post" id="seoautoform">
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />
			<input type="hidden" name="submitted" value="1" /> 
		
			<div id="page_introduction" class="postbox">
				<h2>WPA SEO Auto Linker</h2>
				<div class="inside">
					<p><?php _e('With WPA SEO Auto Linker you can easily add (automatically) links for keywords and phrases in posts, pages and comments and link them to corresponding posts, pages, categories, tags or any URL. Set the following settings to your own needs and let WPA SEO Auto Linker do the work for you.','wpa-seo-auto-linker'); ?></p>
					<p><?php _e('If you find any bugs or have ideas for the plugin, please let me know at:','wpa-seo-auto-linker'); ?> <a href="http://wordpress.org/support/plugin/wpa-seo-auto-linker" title="<?php _e('Post your found bug or idea at WordPress.org','wpa-seo-auto-linker'); ?>" target="_blank">http://wordpress.org/support/plugin/wpa-seo-auto-linker</a>.</p>
				</div>
			</div>
			
			<div id="page_settings" class="postbox">
				<h3><?php _e('Custom Keywords','wpa-seo-auto-linker');?></h3>
				<div class="inside">
					<p><?php _e('Here you can enter manually the extra keywords you want to automaticaly link. Use comma to seperate keywords and add target url at the end. Use a new line for new url and set of keywords. You can have these keywords link to any url, not only your site.','wpa-seo-auto-linker');?></p>
					<table class="form-table">
						<tbody>
							<tr>
								<th><?php _e('Keywords & links','wpa-seo-auto-linker'); ?>:</th>
								<td><textarea name="customkey" id="customkey" rows="10" cols="90"  ><?php echo esc_attr($customkey); ?></textarea><br/><span class="description"><?php _e('Example')?>:<br/><?php _e('google webmaster, http://www.google.com/webmasters/','wpa-seo-auto-linker');?><br /><?php _e('wiki, wikipedia, http://wikipedia.org','wpa-seo-auto-linker');?></span></td>
							</tr>
							<tr>
								<th><?php _e('Grouped keywords','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="customkey_preventduplicatelink" <?php echo esc_attr($customkey_preventduplicatelink);?> /> <strong><?php _e('Prevent duplicate','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('Prevent Duplicate links for grouped keywords (will link only first of the keywords found in text).','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Load from URL','wpa-seo-auto-linker'); ?>:</th>
								<td><input type="text" name="customkey_url" size="90" value="<?php echo esc_url($customkey_url);?>" /><br/><span class="description"><?php _e('Load custom keywords from a URL. This appends to the list above.','wpa-seo-auto-linker');?></span></td>
							</tr>
						</tbody>
					</table>					
					<input class="wpa-button" type="submit" name="Submit2" value="<?php _e('Save all settings','wpa-seo-auto-linker'); ?>" />
				</div>
			</div>
			
			<div id="page_internal_links" class="postbox">
				<h3><?php _e('Internal Links','wpa-seo-auto-linker'); ?></h3>
				<div class="inside">
					<p><?php _e('WPA SEO Auto Linker can process your posts, pages and comments in search for keywords to automatically interlink.','wpa-seo-auto-linker'); ?></p>
					<table class="form-table">
						<tbody>
							<tr>
								<th><?php _e('Posts','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="post" <?php echo esc_attr($post); ?>/> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><label><input type="checkbox" name="postself" <?php echo esc_attr($postself); ?>/> <?php _e('Allow self linking','wpa-seo-auto-linker'); ?></label></td>
							</tr>
							<tr>
								<th><?php _e('Pages','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="page" <?php echo esc_attr($page); ?>/> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><label><input type="checkbox" name="pageself" <?php echo esc_attr($pageself); ?>/> <?php _e('Allow self linking','wpa-seo-auto-linker'); ?></label></td>
							</tr>
							<tr>
								<th><?php _e('Comments','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="comment" <?php echo esc_attr($comment); ?> /> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('May slow down performance.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('RSS feeds','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="allowfeed" <?php echo esc_attr($allowfeed);?> /> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('Allow processing of RSS feeds. WPA SEO Auto Linker will embed links in all posts in your RSS feed (according to other options).','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Max Links','wpa-seo-auto-linker'); ?>:</th>
								<td><input type="text" name="maxlinks" size="2" value="<?php echo esc_attr($maxlinks); ?>"/><br/><span class="description"><?php _e('You can limit the maximum number of different links that will be generated per post. Set to 0 for no limit.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Max Keyword Links','wpa-seo-auto-linker'); ?>:</th>
								<td><input type="text" name="maxsingle" size="2" value="<?php echo esc_attr($maxsingle); ?>"/><br/><span class="description"><?php _e('You can limit the maximum number of links created with the same keyword. Set to 0 for no limit.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Max Same URLs','wpa-seo-auto-linker'); ?>:</th>
								<td><input type="text" name="maxsingleurl" size="2" value="<?php echo esc_attr($maxsingleurl); ?>"/><br/><span class="description"><?php _e('Limit number of same URLs the plugin will link to. Works only when Max Keyword Links above is set to 1. Set to 0 for no limit.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Case sensitive','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="casesens" <?php echo esc_attr($casesens);?> /> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('Set whether matching should be case sensitive.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
						</tbody>
					</table>
					<input class="wpa-button" type="submit" name="Submit1" value="<?php _e('Save all settings','wpa-seo-auto-linker'); ?>" />
				</div>
			</div>
			
			<div id="page_internal_links" class="postbox">
				<h3><?php _e('Excluding','wpa-seo-auto-linker');?></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr>
								<th><?php _e('Heading','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="excludeheading" <?php echo esc_attr($excludeheading); ?>/> <strong><?php _e('Exclude','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('Prevent linking in heading tags (h1, h2, h3, h4, h5 and h6).','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Ignore posts / pages','wpa-seo-auto-linker'); ?>:</th>
								<td><div class="tags"><input id="ign_pp" type="text" name="ignorepost" size="90" value="<?php echo esc_attr($ignorepost);?>" placeholder="<?php _e('Add a tag','wpa-seo-auto-linker'); ?>" /></div><br/><br/><span class="description"><?php _e('You may wish to forbid automatic linking on certain posts or pages. Separate them by comma (ID, slug or name).','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Ignore keywords','wpa-seo-auto-linker'); ?>:</th>
								<td><div class="tags"><input id="ign_key" type="text" name="ignore" size="90" value="<?php echo esc_attr($ignore);?>" placeholder="<?php _e('Add a tag','wpa-seo-auto-linker'); ?>"/></div><br/><br/><span class="description"><?php _e('You may wish to ignore certain words or phrases from automatic linking. Separate them by comma.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Single posts / pages only','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="onlysingle" <?php echo esc_attr($onlysingle); ?>/> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('To reduce database load you can choose to have WPA SEO Auto Linker work only on single posts and pages (for example not on main page or archives).','wpa-seo-auto-linker'); ?></span></td>
							</tr>
						</tbody>
					</table>
					<input class="wpa-button" type="submit" name="Submit3" value="<?php _e('Save all settings','wpa-seo-auto-linker'); ?>" />
				</div>
			</div>
		
			<div id="page_internal_links" class="postbox">
				<h3><?php _e('Targeting','wpa-seo-auto-linker'); ?></h3>
				<div class="inside">
					<p><?php _e('The targets WPA SEO Auto Linker should consider. The match will be based on post/page title or category/tag name, case insensitive.','wpa-seo-auto-linker'); ?></p>
					<table class="form-table">
						<tbody>
							<tr>
								<th><?php _e('Posts','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="lposts" <?php echo esc_attr($lposts); ?> /> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('Enable to link internal links to posts.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Pages','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="lpages" <?php echo esc_attr($lpages); ?> /> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('Enable to link internal links to pages.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Categories','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="lcats" <?php echo esc_attr($lcats); ?> /> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('Enable to link internal links to categories.','wpa-seo-auto-linker'); ?> <?php _e('May slow down performance.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Tags','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="ltags" <?php echo esc_attr($ltags); ?> /> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('Enable to link internal links to tags.','wpa-seo-auto-linker'); ?> <?php _e('May slow down performance.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Minimum categories / tags','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="text" name="minusage" size="2" value="<?php echo esc_attr($minusage); ?>"/> </label><br/><span class="description"><?php _e('Only link categories and tags that have been used the above number of times or more.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Add nofollow','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="nofolo" <?php echo esc_attr($nofolo);?>/> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('Add a nofollow attribute to the external links.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
							<tr>
								<th><?php _e('Open in new window','wpa-seo-auto-linker'); ?>:</th>
								<td><label><input type="checkbox" name="blanko" <?php echo esc_attr($blanko);?>/> <strong><?php _e('Enable','wpa-seo-auto-linker'); ?></strong></label><br/><span class="description"><?php _e('Open the external links in a new window.','wpa-seo-auto-linker'); ?></span></td>
							</tr>
						</tbody>
					</table>
					<input class="wpa-button" type="submit" name="Submit4" value="<?php _e('Save all settings','wpa-seo-auto-linker'); ?>" />
				</div>
			</div>
			
			<div id="page_footer" class="postbox">
				<h3>Running WPA SEO Auto Linker version 0.2</h3>
				<div class="inside">
					<p><?php _e('Like what we do? <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5LRFCEJLZQW7A" target="_blank">Buy us a drink, or a couple of drinks</a>','wpa-seo-auto-linker'); ?>.</p>
				</div>
			</div>
			
		</form>
	 </div>
</div>