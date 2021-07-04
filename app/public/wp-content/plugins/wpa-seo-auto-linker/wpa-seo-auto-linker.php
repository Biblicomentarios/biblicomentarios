<?php
/*
Plugin Name: WPA SEO Auto Linker
Plugin URI: https://www.wpassist.tech/wordpress-plugins/wpa-seo-auto-linker/
Version: 1.2
Author: Arjan Olsder
Author URI: https://www.wpassist.tech
Description: WPA SEO Auto Linker is based on the original SEO Auto Links plugin by Maarten Brakkee. It allows you to setup a list of keywords with default internal and external links. Development of the original plugin seems to have ended as it broke after migrating to PHP 7. If you have a deactivated version of the original plugin, keep it deactivated. Install this one and the original settings will be used.
*/

if ( !class_exists('SEOAutoLinks') ) :
	
class SEOAutoLinks {
	var $SEOAutoLinks_DB_option = 'SEOAutoLinks';
	var $SEOAutoLinks_options; 
	
	function SEOAutoLinks() {	
	  $options = $this->get_options();
	  if ($options) {
			if ($options['post'] || $options['page'])		
				add_filter('the_content',  array(&$this, 'SEOAutoLinks_the_content_filter'), 10);	
			if ($options['comment'])						
				add_filter('comment_text', array(&$this, 'SEOAutoLinks_comment_text_filter'), 10);	
		}
		add_action('create_category', array(&$this, 'SEOAutoLinks_delete_cache'));
		add_action('edit_category', array(&$this,'SEOAutoLinks_delete_cache'));
		add_action('edit_post', array(&$this,'SEOAutoLinks_delete_cache'));
		add_action('save_post', array(&$this,'SEOAutoLinks_delete_cache'));
		add_action('admin_menu', array(&$this, 'SEOAutoLinks_admin_menu'));
		load_plugin_textdomain('wpa-seo-auto-linker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
		add_action('admin_enqueue_scripts', array(&$this, 'SEOAutoLinks_admin_menu_load_scripts'));
	}

function SEOAutoLinks_process_text($text, $mode) {

	global $wpdb, $post;
	
	$options = $this->get_options();
	$links=0;
	
	if (is_feed() && !$options['allowfeed'])
		return $text;
	else if ($options['onlysingle'] && !(is_single() || is_page()))
		return $text;
        
    $arrignorepost=$this->explode_trim(",", ($options['ignorepost']));
	
    if (is_page($arrignorepost) || is_single($arrignorepost)) {
        return $text;
    }
    
	if (!$mode) {
		if ($post->post_type=='post' && !$options['post'])
			return $text;
		else if ($post->post_type=='page' && !$options['page'])
			return $text;
		
		if (($post->post_type=='page' && !$options['pageself']) || ($post->post_type=='post' && !$options['postself'])) {
			$thistitle=$options['casesens'] ? $post->post_title : strtolower($post->post_title);
			$thisurl=trailingslashit(get_permalink($post->ID));
		} else {
			$thistitle='';
			$thisurl='';
		}
	}

	$maxlinks=($options['maxlinks']>0) ? $options['maxlinks'] : 0;	
	$maxsingle=($options['maxsingle']>0) ? $options['maxsingle'] : -1;
	$maxsingleurl=($options['maxsingleurl']>0) ? $options['maxsingleurl'] : 0;
	$minusage = ($options['minusage']>0) ? $options['minusage'] : 1;

	$urls = array();
		
	$arrignore=$this->explode_trim(",", ($options['ignore']));
	if ($options['excludeheading'] == "on") {
		 $text = preg_replace_callback('%(<h.*?>)(.*?)(</h.*?>)%si',
   function($m) { 
      return $m[1].SEOAutoInSpecChar($m[2]).$m[3];
    },
       $text
    );
	}
	
	$reg_post		=	$options['casesens'] ? '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))($name)/msU' : '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))($name)/imsU';	
	$reg			=	$options['casesens'] ? '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))\b($name)\b/msU' : '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))\b($name)\b/imsU';
	$strpos_fnc		=	$options['casesens'] ? 'strpos' : 'stripos';
	
	$text = " $text ";

    if (!empty($options['customkey_url'])) {
        $now = time();
		
        if ($options['customkey_url_datetime']){
            $last_update = $options['customkey_url_datetime'];
        } else {
            $last_update = 0;
        }
		
        if ($now - $last_update > 86400) {
            $body = wp_remote_retrieve_body(wp_remote_get($options['customkey_url']));
            $options['customkey_url_value'] = strip_tags($body);
            $options['customkey_url_datetime'] = $now;
            update_option($this->SEOAutoLinks_DB_option, $options);
        }
		
        $options['customkey'] = $options['customkey'] . "\n" . $options['customkey_url_value'];
    }
	
	// custom keywords
	if (!empty($options['customkey'])) {		
		$kw_array = array();
		
		foreach (explode("\n", $options['customkey']) as $line) {
			if($options['customkey_preventduplicatelink'] == TRUE) {
				$line = trim($line);
				$lastDelimiterPos=strrpos($line, ',');
				$url = substr($line, $lastDelimiterPos + 1 );
				$keywords = substr($line, 0, $lastDelimiterPos);
				
				if(!empty($keywords) && !empty($url)){
					$kw_array[$keywords] = $url;
				}
				
				$keywords='';
				$url='';
			} else {
				$chunks = array_map('trim', explode(",", $line));
				$total_chuncks = count($chunks);
				if($total_chuncks > 2) {
					$i = 0;
					$url = $chunks[$total_chuncks-1];
					while($i < $total_chuncks-1) {
						if (!empty($chunks[$i])) $kw_array[$chunks[$i]] = $url;
							$i++;
					}
				} else {
					list($keyword, $url) = array_map('trim', explode(",", $line, 2));
					if (!empty($keyword)) $kw_array[$keyword] = $url;
				}	
			}
		}
		
		foreach ($kw_array as $name=>$url) {
			if ((!$maxlinks || ($links < $maxlinks)) && (trailingslashit($url)!=$thisurl) && !in_array( $options['casesens'] ? $name : strtolower($name), $arrignore) && (!$maxsingleurl || $urls[$url]<$maxsingleurl) ) {
				if (($options['customkey_preventduplicatelink'] == TRUE) || $strpos_fnc($text, $name) !== false) {
					$name= preg_quote($name, '/');
					if($options['customkey_preventduplicatelink'] == TRUE) $name = str_replace(',','|',$name); //Modifying RegExp for count all grouped keywords as the same one
					
					$replace="<a title=\"$1\" href=\"$url\">$1</a>";
					$regexp=str_replace('$name', $name, $reg);	
					$newtext = preg_replace($regexp, $replace, $text, $maxsingle);			
					if ($newtext!=$text) {							
						$links++;
						$text=$newtext;
                        if (!isset($urls[$url])) $urls[$url]=1; else $urls[$url]++;
					}	
				}
			}		
		}
	}

	
	// posts and pages
	if ($options['lposts'] || $options['lpages']) {
		if ( !$posts = wp_cache_get( 'seo-links-posts', 'wpa-seo-auto-linker' ) ) {
			$query="SELECT post_title, ID, post_type FROM $wpdb->posts WHERE post_status = 'publish' AND LENGTH(post_title)>3 ORDER BY LENGTH(post_title) DESC LIMIT 2000";
			$posts = $wpdb->get_results($query);
			wp_cache_add( 'seo-links-posts', $posts, 'wpa-seo-auto-linker', 86400 );
		}
	
		foreach ($posts as $postitem) {
			if ((($options['lposts'] && $postitem->post_type=='post') || ($options['lpages'] && $postitem->post_type=='page')) && (!$maxlinks || ($links < $maxlinks))  && (($options['casesens'] ? $postitem->post_title : strtolower($postitem->post_title))!=$thistitle) && (!in_array( ($options['casesens'] ? $postitem->post_title : strtolower($postitem->post_title)), $arrignore))) {
				if ($strpos_fnc($text, $postitem->post_title) !== false) {		// credit to Dominik Deobald
					$name = preg_quote($postitem->post_title, '/');		
					
					$regexp=str_replace('$name', $name, $reg);	
					$replace='<a title="$1" href="$$$url$$$">$1</a>';
				
					$newtext = preg_replace($regexp, $replace, $text, $maxsingle);
					if ($newtext!=$text) {		
						$url = get_permalink($postitem->ID);
						if (!$maxsingleurl || $urls[$url]<$maxsingleurl) {
						  $links++;
						  $text=str_replace('$$$url$$$', $url, $newtext);	
						  if (!isset($urls[$url])) $urls[$url]=1; else $urls[$url]++;
						}
					}
				}
			}
		}
	}
	
	// categories
	if ($options['lcats']) {
		if ( !$categories = wp_cache_get( 'seo-links-categories', 'wpa-seo-auto-linker' ) ) {
			$query="SELECT $wpdb->terms.name, $wpdb->terms.term_id FROM $wpdb->terms LEFT JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id WHERE $wpdb->term_taxonomy.taxonomy = 'category'  AND LENGTH($wpdb->terms.name)>3 AND $wpdb->term_taxonomy.count >= $minusage ORDER BY LENGTH($wpdb->terms.name) DESC LIMIT 2000";
			$categories = $wpdb->get_results($query);
			wp_cache_add( 'seo-links-categories', $categories, 'wpa-seo-auto-linker',86400 );
		}
	
		foreach ($categories as $cat) {
			if ((!$maxlinks || ($links < $maxlinks)) &&  !in_array( $options['casesens'] ?  $cat->name : strtolower($cat->name), $arrignore)) {
				if ($strpos_fnc($text, $cat->name) !== false) {		// credit to Dominik Deobald
					$name= preg_quote($cat->name, '/');	
					$regexp=str_replace('$name', $name, $reg);	;
					$replace='<a title="$1" href="$$$url$$$">$1</a>';
				
					$newtext = preg_replace($regexp, $replace, $text, $maxsingle);
					if ($newtext!=$text) {						
						$url = (get_category_link($cat->term_id));	
						if (!$maxsingleurl || $urls[$url]<$maxsingleurl) {			
						  $links++;
						  $text=str_replace('$$$url$$$', $url, $newtext);
						  if (!isset($urls[$url])) $urls[$url]=1; else $urls[$url]++;
						}
					}
				}
			}		
		}
	}
	
	// tags
	if ($options['ltags']) {
		if ( !$tags = wp_cache_get( 'seo-links-tags', 'wpa-seo-auto-linker' ) ) {
			$query="SELECT $wpdb->terms.name, $wpdb->terms.term_id FROM $wpdb->terms LEFT JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id WHERE $wpdb->term_taxonomy.taxonomy = 'post_tag'  AND LENGTH($wpdb->terms.name)>3 AND $wpdb->term_taxonomy.count >= $minusage ORDER BY LENGTH($wpdb->terms.name) DESC LIMIT 2000";	
			$tags = $wpdb->get_results($query);
			wp_cache_add( 'seo-links-tags', $tags, 'wpa-seo-auto-linker',86400 );
		}
		
		foreach ($tags as $tag) {
			if ((!$maxlinks || ($links < $maxlinks)) && !in_array( $options['casesens'] ? $tag->name : strtolower($tag->name), $arrignore) ) {
				if ($strpos_fnc($text, $tag->name) !== false) {		// credit to Dominik Deobald
					$name = preg_quote($tag->name, '/');	
					$regexp=str_replace('$name', $name, $reg);	;
					$replace='<a title="$1" href="$$$url$$$">$1</a>';
									
					$newtext = preg_replace($regexp, $replace, $text, $maxsingle);
					if ($newtext!=$text) {
						$url = (get_tag_link($tag->term_id));
						if (!$maxsingleurl || $urls[$url]<$maxsingleurl) {			
						  $links++;
						  $text=str_replace('$$$url$$$', $url, $newtext);
                          if (!isset($urls[$url])) $urls[$url]=1; else $urls[$url]++;
						}
					}
				}
			}
		}
	}
	
	if ($options['excludeheading'] == "on") {
		
		$text = preg_replace_callback('%(<h.*?>)(.*?)(</h.*?>)%si', function($m) { 
			return $m[1].SEOAutoReSpecChar($m[2]).$m[3];
      	},
      $text
    );
		$text = stripslashes($text);
	}
	return trim( $text );

} 

function SEOAutoLinks_the_content_filter($text) {
	return SEOAutoTextFilter($this->get_options(),$this->SEOAutoLinks_process_text($text, 0));
}

function SEOAutoLinks_comment_text_filter($text) {
	return SEOAutoTextFilter($this->get_options(),$this->SEOAutoLinks_process_text($text, 1));
}
	
function explode_trim($separator, $text) {
    $arr = explode($separator, $text);
    
    $ret = array();
    foreach($arr as $e) {        
      $ret[] = trim($e);        
    }
    return $ret;
}

// Setting default values for fresh installations
function get_options() {
   
	$options = array(
		'post' => 'on',
		'postself' => '',
		'page' => 'on',
		'pageself' => '',
		'comment' => '',
		'excludeheading' => 'on', 
		'lposts' => 'on', 
		'lpages' => 'on',
		'lcats' => '', 
		'ltags' => '', 
		'ignore' => 'about', 
		'ignorepost' => 'contact', 
		'maxlinks' => 3,
		'maxsingle' => 1,
		'minusage' => 1,
		'customkey' => '',
		'customkey_preventduplicatelink' => FALSE,
		'customkey_url' => '',
		'customkey_url_value' => '',
		'customkey_url_datetime' => '',
		'nofoln' =>'',
		'nofolo' =>'',
		'blankn' =>'',
		'blanko' =>'',
		'onlysingle' => 'on',
		'casesens' =>'',
		'allowfeed' => '',
		'maxsingleurl' => '1'
	);
 
	$saved = get_option($this->SEOAutoLinks_DB_option);

	if (!empty($saved)) {
		foreach ($saved as $key => $option)
			$options[$key] = $option;
	}

	if ($saved != $options)	
		update_option($this->SEOAutoLinks_DB_option, $options);

	return $options;
}


// Installation script for WPA SEO Auto Linker, registering the options field in the Wordpress table with default values
function install() {
	$SEOAutoLinks_options = $this->get_options();
}


function handle_options() {
	$options = $this->get_options();
	if (isset($_POST['submitted']) ) {
		check_admin_referer('wpa-seo-auto-linker');		
		
																					// In order of appearance on the admin form
		
		$options['customkey']		= strip_tags($_POST['customkey']);				// Custom keywords and links
		$options['customkey_preventduplicatelink']=sanitize_key($_POST['customkey_preventduplicatelink']);	// Prevent duplicates
		$options['customkey_url']	= sanitize_text_field($_POST['customkey_url']);	// Load URL's from TXT file
		
		$options['post']			= sanitize_key($_POST['post']);					// Enable posts
		$options['postself']		= sanitize_key($_POST['postself']);				// Enable self linking in posts	
		$options['page']			= sanitize_key($_POST['page']);					// Enable pages
		$options['pageself']		= sanitize_key($_POST['pageself']);				// Enable self linking in pages
		$options['comment']			= sanitize_key($_POST['comment']);				// Enable comments
		$options['allowfeed']		= sanitize_key($_POST['allowfeed']);			// Enable RSS Feeds
		$options['maxlinks']		= sanitize_key($_POST['maxlinks']);				// Max links per post, page etc
		$options['maxsingle']		= sanitize_key($_POST['maxsingle']);			// Max keyword links
		$options['maxsingleurl']	= sanitize_key($_POST['maxsingleurl']);			// Max same URLs
		$options['casesens']		= sanitize_key($_POST['casesens']);				// Toggle case sensitivity
		
		$options['excludeheading']	= sanitize_key($_POST['excludeheading']);		// Exclude headers
		$options['ignorepost']		= sanitize_text_field($_POST['ignorepost']);	// Ignore posts
		$options['ignore']			= sanitize_text_field($_POST['ignore']);		// Ignore keywords
		$options['onlysingle']		= sanitize_key($_POST['onlysingle']);			// Only single, ignore frontpage, archives etc
		
		$options['lposts']			= sanitize_key($_POST['lposts']);				// Auto link post titles
		$options['lpages']			= sanitize_key($_POST['lpages']);				// Auto link page titles
		$options['lcats']			= sanitize_key($_POST['lcats']);				// Auto link category pages
		$options['ltags']			= sanitize_key($_POST['ltags']);				// Auto link tag pages
		$options['minusage']		= sanitize_key($_POST['minusage']);		// Maximum links to post, page etc
		$options['nofoln']			= sanitize_key($_POST['nofoln']);				//
		$options['nofolo']			= sanitize_key($_POST['nofolo']);				// Add rel nofolow to external links
		$options['blankn']			= sanitize_key($_POST['blankn']);				//
		$options['blanko']			= sanitize_key($_POST['blanko']);				// Add target _blank to external links
		
		update_option($this->SEOAutoLinks_DB_option, $options);
		$this->SEOAutoLinks_delete_cache(0);
		echo '<div class="updated"><p>';
		_e('Plugin settings saved.','wpa-seo-auto-linker');
		echo '</p></div>';
	}

	$action_url 	= $_SERVER['REQUEST_URI'];	

	$post			= $options['post']=='on'?'checked':'';
	$postself		= $options['postself']=='on'?'checked':'';
	$page			= $options['page']=='on'?'checked':'';
	$pageself		= $options['pageself']=='on'?'checked':'';
	$comment		= $options['comment']=='on'?'checked':'';
	$excludeheading = $options['excludeheading']=='on'?'checked':'';
	$lposts			= $options['lposts']=='on'?'checked':'';
	$lpages			= $options['lpages']=='on'?'checked':'';
	$lcats			= $options['lcats']=='on'?'checked':'';
	$ltags			= $options['ltags']=='on'?'checked':'';
	$ignore			= $options['ignore'];
	$ignorepost		= $options['ignorepost'];
	$maxlinks		= $options['maxlinks'];
	$maxsingle		= $options['maxsingle'];
	$maxsingleurl	= $options['maxsingleurl'];
	$minusage		= $options['minusage'];
	$customkey		= stripslashes($options['customkey']);
	$customkey_url 	= stripslashes($options['customkey_url']);
	$customkey_preventduplicatelink = $options['customkey_preventduplicatelink'] == TRUE ? 'checked' : '';
	$nofoln			= $options['nofoln']=='on'?'checked':'';
	$nofolo			= $options['nofolo']=='on'?'checked':'';
	$blankn			= $options['blankn']=='on'?'checked':'';
	$blanko			= $options['blanko']=='on'?'checked':'';
	$onlysingle		= $options['onlysingle']=='on'?'checked':'';
	$casesens		= $options['casesens']=='on'?'checked':'';
	$allowfeed		= $options['allowfeed']=='on'?'checked':'';

	if (!is_numeric($minusage)) {
		$minusage = 1;
	}
	
	$nonce			= wp_create_nonce('wpa-seo-auto-linker');
	
	/**
	 * Require admin settings page
	 */
	require_once dirname(__FILE__) . '/wpa-seo-auto-linker-admin.php';
}

// Register WPA SEO Auto Linker into the Wordpress settings menu
function SEOAutoLinks_admin_menu() {
	global $seoauto_adminmenu;
	$seoauto_adminmenu = add_options_page('WPA SEO Auto Linker Options', 'WPA SEO Auto Linker', 'manage_options', basename(__FILE__), array(&$this, 'handle_options'));
}

// Load styles and javascripts for admin page display
function SEOAutoLinks_admin_menu_load_scripts($hook) {
	global $seoauto_adminmenu;
	if( $hook != $seoauto_adminmenu ) 
		return;
	wp_enqueue_script('tagsjs', plugins_url( '/js/load.js', __FILE__ ) );
	wp_enqueue_style('tagscss', plugins_url( '/css/wpa-seo-auto-linker-style.css', __FILE__ ) );
}

// Delete WPA SEO Auto Linker cache
function SEOAutoLinks_delete_cache($id) {
	wp_cache_delete( 'seo-links-categories', 'wpa-seo-auto-linker' );
	wp_cache_delete( 'seo-links-tags', 'wpa-seo-auto-linker' );
	wp_cache_delete( 'seo-links-posts', 'wpa-seo-auto-linker' );
}


}

endif; 

if (class_exists('SEOAutoLinks') ) :
	$SEOAutoLinks = new SEOAutoLinks();
	if (isset($SEOAutoLinks)) {
		register_activation_hook( __FILE__, array(&$SEOAutoLinks, 'install') );
	}
endif;

	/**
	 * Require WPA SEO Auto Linker functions
	 */
	require_once dirname(__FILE__) . '/wpa-seo-auto-linker-functions.php';
?>