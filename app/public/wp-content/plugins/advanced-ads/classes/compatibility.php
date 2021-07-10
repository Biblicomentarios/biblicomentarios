<?php
/**
 * Compatibility fixes with other plugins.
 */
class Advanced_Ads_Compatibility {

	/**
	 * Advanced_Ads_Compatibility constructor.
	 */
	public function __construct() {
		// Elementor plugin.
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			add_filter(
				'advanced-ads-placement-content-injection-xpath',
				array(
					$this,
					'content_injection_elementor',
				),
				10,
				1
			);
		}
		if ( defined( 'WP_ROCKET_VERSION' ) ) {
			add_filter( 'rocket_excluded_inline_js_content', array( $this, 'exclude_inline_js' ) );
		}
		// WPML.
		add_filter( 'wpml_admin_language_switcher_active_languages', array( $this, 'wpml_language_switcher' ) );
		// WordPress SEO by Yoast.
		add_filter( 'wpseo_sitemap_entry', array( $this, 'wordpress_seo_noindex_ad_attachments' ), 10, 3 );
		// Add shortcode for MailPoet.
		add_filter( 'mailpoet_newsletter_shortcode', array( 'Advanced_Ads_Compatibility', 'mailpoet_ad_shortcode' ), 10, 5 );

		// Enable Advanced Custom Fields on ad edit pages.
		if ( class_exists( 'ACF', false ) ) {
			add_filter( 'advanced-ads-ad-edit-allowed-metaboxes', array( $this, 'advanced_custom_fields_box' ) );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_dequeue_scripts_and_styles' ), 100 );

		if ( defined( 'BORLABS_COOKIE_VERSION' ) ) {
			// Check if Verification code & Auto ads ads can be displayed.
			add_filter( 'advanced-ads-can-display-ads-in-header', array( $this, 'borlabs_cookie_can_add_auto_ads_code' ), 10 );
		}
	}

	/**
	 * Modify xPath expression for Elementor plugin.
	 * The plugin does not wrap newly created text in 'p' tags.
	 *
	 * @param string $tag xpath tag.
	 * @return string xPath expression
	 */
	public function content_injection_elementor( $tag ) {
		if ( 'p' === $tag ) {
			// 'p' or 'div.elementor-widget-text-editor' without nested 'p'
			$tag = "*[self::p or self::div[@class and contains(concat(' ', normalize-space(@class), ' '), ' elementor-widget-text-editor ') and not(descendant::p)]]";
		}

		return $tag;
	}

	/**
	 * Prevent the 'advanced_ads_ready' function declaration from being merged with other JS
	 * and outputted into the footer. This is needed because WP Rocket does not output all
	 * the code that depends on this function into the footer.
	 *
	 * @param array $pattern Patterns to match in inline JS content.
	 * @return array
	 */
	public function exclude_inline_js( $pattern ) {
		$pattern[] = 'advanced_ads_ready';

		return $pattern;
	}

	/**
	 * Compatibility with WPML
	 * show only all languages in language switcher on Advanced Ads pages if ads and groups are translated
	 *
	 * @param array $active_languages languages that can be used in language switcher.
	 * @return array
	 */
	public function wpml_language_switcher( $active_languages ) {
		global $sitepress;
		$screen = get_current_screen();
		if ( ! isset( $screen->id ) ) {
			return $active_languages;
		}

		switch ( $screen->id ) {
			// check if we are on a group edit page and ad group translations are disabled.
			case 'advanced-ads_page_advanced-ads-groups':
				$translatable_taxonomies = $sitepress->get_translatable_taxonomies();
				if ( ! is_array( $translatable_taxonomies ) || ! in_array( 'advanced_ads_groups', $translatable_taxonomies, true ) ) {
					return array();
				}
				break;
			// check if Advanced Ads ad post type is translatable.
			case 'edit-advanced_ads': // overview page.
			case 'advanced_ads': // edit page.
				$translatable_documents = $sitepress->get_translatable_documents();
				if ( empty( $translatable_documents['advanced_ads'] ) ) {
					return array();
				}
				break;
		}

		return $active_languages;
	}

	/**
	 * WordPress SEO: remove attachments attached to ads from `/attachment-sitemap.xml`.
	 *
	 * @param array  $url  Array of URL parts.
	 * @param string $type URL type.
	 * @param object $post WP_Post object of attachment.
	 * @return array|bool Unmodified array of URL parts or false to remove URL.
	 */
	public function wordpress_seo_noindex_ad_attachments( $url, $type, $post ) {
		if ( 'post' !== $type ) {
			return $url;
		}

		static $ad_ids = null;
		if ( null === $ad_ids ) {
			$ad_ids = Advanced_Ads::get_instance()->get_model()->get_ads(
				array(
					'post_status' => 'any',
					'fields'      => 'ids',
				)
			);
		}

		if ( isset( $post->post_parent ) && in_array( $post->post_parent, $ad_ids, true ) ) {
			return false;
		}

		return $url;
	}

	/**
	 * Display an ad or ad group in a newsletter created by MailPoet.
	 * e.g., [custom:ad:123] to display ad with the ID 123
	 * [custom:ad_group:345] to display ad group with the ID 345
	 *
	 * @param string $shortcode shortcode that placed the ad.
	 * @param mixed  $newsletter unused.
	 * @param mixed  $subscriber unused.
	 * @param mixed  $queue unused.
	 * @param string $newsletter_body unused.
	 *
	 * @return string
	 */
	public static function mailpoet_ad_shortcode( $shortcode, $newsletter, $subscriber, $queue, $newsletter_body ) {

		// display an ad group.
		if ( 0 === strpos( $shortcode, '[custom:ad_group:' ) ) {
			// get ad group ID.
			preg_match( '/\d+/', $shortcode, $matches );
			$group_id = $matches[0];

			// is returning an empty string when the ad group is not found good UI?
			if ( empty( $group_id ) ) {
				return '';
			}

			// only display if the ad group type could work, i.e. default (random) and ordered.
			$ad_group = new Advanced_Ads_Group( $group_id );
			if ( isset( $ad_group->type ) && in_array( $ad_group->type, array( 'default', 'ordered' ), true ) ) {
				return get_ad_group( $group_id );
			}

			return '';

			// display individual ad.
		} elseif ( 0 === strpos( $shortcode, '[custom:ad:' ) ) {
			// get ad ID.
			preg_match( '/\d+/', $shortcode, $matches );
			$ad_id = $matches[0];

			// is returning an empty string when the ad is not found good UI?
			if ( empty( $ad_id ) ) {
				return '';
			}

			$ad = new Advanced_Ads_Ad( $ad_id );
			// only display if the ad type could work, i.e. plain text and image ads.
			if ( isset( $ad->type ) && in_array( $ad->type, array( 'plain', 'image' ), true ) ) {
				return get_ad( $ad_id );
			}

			return '';
		} else {
			// always return the shortcode if it doesn't match your own!
			return $shortcode;
		}
	}

	/**
	 * Check if placements of type other than `header` can be injected during `wp_head` action.
	 */
	public static function can_inject_during_wp_head() {
		// the "Thrive Theme Builder" theme.
		if ( did_action( 'before_theme_builder_template_render' ) && ! did_action( 'after_theme_builder_template_render' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Dequeue scripts and styles to prevent layout issues.
	 */
	public function admin_dequeue_scripts_and_styles() {
		if ( ! Advanced_Ads_Admin::screen_belongs_to_advanced_ads() ) {
			return;
		}

		// Dequeue the css file enqueued by the JNews theme.
		if ( defined( 'JNEWS_THEME_URL' ) ) {
			wp_dequeue_style( 'jnews-admin' );
		}
	}

	/**
	 * Check if Adsense Auto ads code can be added to the header.
	 *
	 * @param bool $can_display if the ad can already be displayed.
	 * @return bool
	 */
	public function borlabs_cookie_can_add_auto_ads_code( $can_display ) {
		if ( ! $can_display ) {
			return false;
		}

		return ! self::borlabs_cookie_adsense_auto_ads_code_exists();
	}

	/**
	 * Check if Adsense Auto ads code is added by the Borlabs Cookie plugin.
	 *
	 * This allows to prevent the "Only one 'enable_page_level_ads' allowed per page" error
	 * that makes impossible to close the "Privacy Preference" window created by the "Borlabs Cookie" plugin.
	 *
	 * @return bool
	 */
	public static function borlabs_cookie_adsense_auto_ads_code_exists() {
		// Cache the result in order to perform the check only once.
		static $result = null;

		if ( null !== $result ) {
			return $result;
		}

		// Set the `autoload` param to `true` so that the class loads both in frontend and backend.
		if ( class_exists( '\BorlabsCookie\Cookie\Frontend\Cookies', true ) ) {
			try {
				$refl_cookies = new ReflectionClass( '\BorlabsCookie\Cookie\Frontend\Cookies' );

				if ( $refl_cookies->hasMethod( 'getInstance' ) && $refl_cookies->hasMethod( 'getAllCookieGroups' ) ) {
					$instance      = $refl_cookies->getMethod( 'getInstance' );
					$cookie_groups = $refl_cookies->getMethod( 'getAllCookieGroups' );

					if ( $instance->isPublic() && $instance->isStatic() && $cookie_groups->isPublic() ) {
						$all_cookies = BorlabsCookie\Cookie\Frontend\Cookies::getInstance()->getAllCookieGroups();
					}
				}
			} catch ( Exception $e ) {
				$result = false;
				return $result;
			}
		}

		if ( empty( $all_cookies ) ) {
			$result = false;
			return $result;
		}

		foreach ( $all_cookies as $cookie_group_data ) {
			if ( ! empty( $cookie_group_data->group_id ) && 'marketing' === $cookie_group_data->group_id
				&& ! empty( $cookie_group_data->cookies ) ) {
				foreach ( $cookie_group_data->cookies as $cookie_data ) {
					if ( ! empty( $cookie_data->cookie_id ) && 'google-adsense' === $cookie_data->cookie_id
						&& ! empty( $cookie_data->opt_in_js ) ) {
						$opt_in_js = $cookie_data->opt_in_js;
					}
				}
			}
		}

		if ( empty( $opt_in_js ) ) {
			$result = false;
			return $result;
		}

		$result = preg_match( '/<script[^>]+data-ad-client/', $opt_in_js ) || false !== strpos( $opt_in_js, 'enable_page_level_ads:' );
		return $result;
	}

	/**
	 * Whitelist meta boxes created by the Advanced Custom Fields plugin on the ad edit pages
	 * when they are dedicated for the "Ad" post type.
	 *
	 * @param array $meta_boxes already whitelisted meta boxes.
	 * @return array
	 */
	public function advanced_custom_fields_box( $meta_boxes ) {

		// fixes an issue reported when ACF class exists, but this function does not
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return $meta_boxes;
		}

		// load ACF field groups dedicated to the Advanced Ads post type
		$groups = acf_get_field_groups( array( 'post_type' => Advanced_Ads::POST_TYPE_SLUG ) );

		if ( is_array( $groups ) && $groups ) {
			foreach ( $groups as $_group ) {
				if ( isset( $_group['key'] ) ) {
					$meta_boxes[] = 'acf-' . $_group['key'];
				}
			}
		}

		return $meta_boxes;
	}
}
