<?php

class Swift_Performance_Meta_Boxes {

	public $fields = array(
		'use-compute-api',
		'smart-lazyload', 'lazy-load-images', 'lazyload-iframes', 'smart-youtube-embed',
		'merge-scripts', 'async-scripts', 'script-delivery', 'minify-scripts', 'use-script-compute-api', 'include-scripts', 'block-scripts', 'extra-javascript', 'extra-javascript-footer', 'server-side-script', 'preload-scripts', 'script-safe-mode', 'lazy-load-scripts',
		'merge-styles', 'critical-css', 'critical-css-mode', 'extra-critical-css', 'extra-css', 'load-full-css-on-scroll', 'disable-full-css', 'inline_critical_css', 'minify-css', 'bypass-css-import', 'include-styles', 'preload-styles', 'preprocess-scripts', 'preprocess-inline-scripts',
		'preload-fonts','exclude-preload-fonts','local-fonts','exclude-local-fonts','font-display', 'exclude-font-display','manual-preload-fonts',
		'smart-render-html', 'html-auto-fix', 'minify-html',
		'disable-emojis', 'dns-prefetch', 'dns-prefetch-js', 'exclude-dns-prefetch',
		'lazyload-shortcode','lazyload-blocks','lazyload-widgets','ajaxify-placeholder','ajaxify',
	);

	public function __construct(){
		$luvmeta = Luv_Framework::fields('meta', array(
			'ajax'	=> true,
			'name'	=> SWIFT_PERFORMANCE_PLUGIN_NAME,
			'nonce_id'  => 'swift_performance_meta',
			'meta_key'	=> 'swift_performance_options',
			'class'	=> 'swift-performance-settings',
			'sections'	=> $this->get_sections(),
			'post_type'	=> Swift_Performance::get_post_types(Swift_Performance::get_option('exclude-post-types'))
		));

		add_filter('luv_framework_save_meta_array', array($this, 'meta_filter'), 10, 2);

		// Override settings
		add_action('template_redirect', array($this, 'override'));
	}

	public function get_sections(){
		$maybe_sections['meta'] = array(
			'title'	=> 'Page Level Settings',
			'icon'	=> 'fas fa-cog',
			'subsections' => array(
				'general' => array(
					'title'	=> esc_html__('General', 'swift-performance'),
					'fields'	=> array(
						array(
							'id' => 'settings-mode',
							'type' => 'hidden',
							'default' => 'advanced'
						),
                                    array(
							'title'	=> esc_html__('Override Global Settings', 'swift-performance'),
							'id'   => 'override-globals',
							'type' => 'switch',
							'default' => 0
						),
					)
				)
			)
		);


		foreach (Swift_Performance::$luvoptions->args['sections'] as $key => $section){
			$maybe_subsections = array();
			foreach ($section['subsections'] as $skey => $subsection){
				$maybe_fields = array();
				foreach ($subsection['fields'] as $field){
					if (in_array($field['id'], $this->fields)){

						$field['default'] = Swift_Performance::get_option($field['id'], '', true);

						if (!isset($field['required'])){
							$field['required'] = array(array('override-globals', '=', 1));
						}
						else {
							if (!is_array($field['required'][0])){
				                        $field['required'] = array($field['required']);
				                  }

							$field['required'][] = array('override-globals', '=', 1);
						}

						if (isset($field['class'])){
							$field['class'] = str_replace('should-clear-cache', '', $field['class']);
						}

						$maybe_fields[] = $field;

					}
				}
				if (!empty($maybe_fields)){
					if (isset($maybe_sections['meta']['subsections'][$skey])){
						$maybe_sections['meta']['subsections'][$skey]['fields'] = array_merge($maybe_sections['meta']['subsections'][$skey]['fields'], $maybe_fields);
					}
					else {
						$maybe_sections['meta']['subsections'][$skey] = array(
							'title'	=> $subsection['title'],
							'fields'	=> $maybe_fields
						);
					}
				}
			}
		}

		return $maybe_sections;
	}

	public function meta_filter($meta, $that){
		foreach ((array)$that->defined_fields as $key => $field){
			$global = Swift_Performance::get_option($field['id'], '', true);
			if(isset($_POST[$that->prefix . $field['id']])){
				$_POST[$that->prefix . $field['id']] = stripslashes_deep($_POST[$that->prefix . $field['id']]);

				// Remove empty elements from array
				if (is_array($_POST[$that->prefix . $field['id']])){
					$_POST[$that->prefix . $field['id']] = array_filter($_POST[$that->prefix . $field['id']]);
				}

				if ($_POST[$that->prefix . $field['id']] == $global || (empty($_POST[$that->prefix . $field['id']]) && empty($global)) ){
					unset($meta[$key]);
				}
			}
		}

		return $meta;
	}

	public function override(){
		$meta = (array)get_post_meta(get_the_ID(), 'swift_performance_options', true);
		if ($meta['override-settings'] == 1){
			foreach ($meta as $key => $value){
				Swift_Performance::set_option($key, $value);
			}
		}
	}

}

return new Swift_Performance_Meta_Boxes();
?>
