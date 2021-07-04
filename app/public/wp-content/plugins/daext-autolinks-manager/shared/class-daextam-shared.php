<?php

/*
 * this class should be used to stores properties and methods shared by the
 * admin and public side of wordpress
 */

class Daextam_Shared
{

    //properties used in add_autolinks()
    private $autolink_id = 0;
    private $autolink_a = array();
    private $parsed_autolink = null;
    private $parsed_post_type = null;
    private $max_number_autolinks_per_post = null;
    private $same_url_limit = null;
    private $autolinks_ca = null;
    private $pb_id = null;
    private $pb_a = null;

    //regex
    public $regex_list_of_gutenberg_blocks = '/^(\s*([A-Za-z0-9-\/]+\s*,\s*)+[A-Za-z0-9-\/]+\s*|\s*[A-Za-z0-9-\/]+\s*)$/';
    public $regex_number_ten_digits = '/^\s*\d{1,10}\s*$/';
    public $number_of_replacements = 0;

    protected static $instance = null;

    private $data = array();

    private function __construct()
    {

	    //Set plugin textdomain
	    load_plugin_textdomain('daext-autolinks-manager', false, 'daext-autolinks-manager/lang/');

		$this->data['slug'] = 'daextam';
		$this->data['ver']  = '1.10.01';
		$this->data['dir']  = substr(plugin_dir_path(__FILE__), 0, -7);
		$this->data['url']  = substr(plugin_dir_url(__FILE__), 0, -7);

		//Here are stored the plugin option with the related default values
		$this->data['options'] = [

			//Database Version -----------------------------------------------------------------------------------------
			$this->get('slug') . "_database_version" => "0",

			//Defaults -------------------------------------------------------------------------------------------------
			$this->get('slug') . '_defaults_category_id' => "0",
			$this->get('slug') . '_defaults_open_new_tab' => "0",
			$this->get('slug') . '_defaults_use_nofollow' => "0",
			$this->get('slug') . '_defaults_post_types' => "",
			$this->get('slug') . '_defaults_categories' => "",
			$this->get('slug') . '_defaults_tags' => "",
			$this->get('slug') . '_defaults_term_group_id' => "",
			$this->get('slug') . '_defaults_case_sensitive_search' => "1",
			$this->get('slug') . '_defaults_left_boundary' => "0",
			$this->get('slug') . '_defaults_right_boundary' => "0",
			$this->get('slug') . '_defaults_limit' => "100",
			$this->get('slug') . '_defaults_priority' => "0",

			//Analysis -------------------------------------------------------------------------------------------------
			$this->get('slug') . '_analysis_set_max_execution_time' => "1",
			$this->get('slug') . '_analysis_max_execution_time_value' => "300",
			$this->get('slug') . '_analysis_set_memory_limit' => "1",
			$this->get('slug') . '_analysis_memory_limit_value' => "512",
			$this->get('slug') . '_analysis_limit_posts_analysis' => "1000",
			$this->get('slug') . '_analysis_post_types' => "",

			//Advanced -------------------------------------------------------------------------------------------------
			$this->get('slug') . '_advanced_enable_autolinks' => "1",
			$this->get('slug') . '_advanced_filter_priority' => "2147483646",
			$this->get('slug') . '_advanced_enable_test_mode' => "0",
			$this->get('slug') . '_advanced_random_prioritization' => "1",
			$this->get('slug') . '_advanced_ignore_self_autolinks' => "1",
			$this->get('slug') . '_advanced_categories_and_tags_verification' => "post",
			$this->get('slug') . '_advanced_general_limit_mode' => "1",
			$this->get('slug') . '_advanced_general_limit_characters_per_autolink' => "200",
			$this->get('slug') . '_advanced_general_limit_amount' => "100",
			$this->get('slug') . '_advanced_same_url_limit' => "100",

			/*
			 * By default the following HTML tags are protected:
			 *
			 * - h1
			 * - h2
			 * - h3
			 * - h4
			 * - h5
			 * - h6
			 * - a
			 * - img
			 * - pre
			 * - code
			 * - table
			 * - iframe
			 * - script
			 */
			$this->get('slug') . '_advanced_protected_tags' => array(
				'h1',
				'h2',
				'h3',
				'h4',
				'h5',
				'h6',
				'a',
				'img',
				'ul',
				'ol',
				'span',
				'pre',
				'code',
				'table',
				'iframe',
				'script'
			),

			/*
			 * By default all the Gutenberg Blocks except the following are protected:
			 *
			 * - Paragraph
			 * - List
			 * - Text Columns
			 */
			$this->get('slug') . '_advanced_protected_gutenberg_blocks' => array(
				//'paragraph',
				'image',
				'heading',
				'gallery',
				//'list',
				'quote',
				'audio',
				'cover-image',
				'subhead',
				'video',
				'code',
				'html',
				'preformatted',
				'pullquote',
				'table',
				'verse',
				'button',
				'columns',
				'more',
				'nextpage',
				'separator',
				'spacer',
				//'text-columns',
				'shortcode',
				'categories',
				'latest-posts',
				'embed',
				'core-embed/twitter',
				'core-embed/youtube',
				'core-embed/facebook',
				'core-embed/instagram',
				'core-embed/wordpress',
				'core-embed/soundcloud',
				'core-embed/spotify',
				'core-embed/flickr',
				'core-embed/vimeo',
				'core-embed/animoto',
				'core-embed/cloudup',
				'core-embed/collegehumor',
				'core-embed/dailymotion',
				'core-embed/funnyordie',
				'core-embed/hulu',
				'core-embed/imgur',
				'core-embed/issuu',
				'core-embed/kickstarter',
				'core-embed/meetup-com',
				'core-embed/mixcloud',
				'core-embed/photobucket',
				'core-embed/polldaddy',
				'core-embed/reddit',
				'core-embed/reverbnation',
				'core-embed/screencast',
				'core-embed/scribd',
				'core-embed/slideshare',
				'core-embed/smugmug',
				'core-embed/speaker',
				'core-embed/ted',
				'core-embed/tumblr',
				'core-embed/videopress',
				'core-embed/wordpress-tv'
			),

			$this->get('slug') . '_advanced_protected_gutenberg_custom_blocks' => "",
			$this->get('slug') . '_advanced_protected_gutenberg_custom_void_blocks' => "",
			$this->get('slug') . '_advanced_supported_terms' => "10",

		];

		add_action('delete_term', array($this, 'delete_term_action'), 10, 3);


	}

    public static function get_instance()
    {

        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;

    }

    //retrieve data
    public function get($index)
    {
        return $this->data[$index];
    }

    /*
     * Add autolinks to the content based on the keyword created with the AIL menu:
     * 
     * 1 - The protected blocks are applied with apply_protected_blocks()
     * 2 - The words to be converted as a link are temporarely replaced with [ail]ID[/ail]
     * 3 - The [al]ID[/al] identifiers are replaced with the actual links
     * 4 - The protected block are removed with the remove_protected_blocks()
     * 5 - The content with applied the autolinks is returned
     * 
     * @param $content The content on which the autolinks should be applied.
     * @param $check_query This parameter is set to True when the method is called inside the loop and is used to
     * verify if we are in a single post.
     * @param $post_type If the autolinks are added from the back-end this parameter is used to determine the post type
     * of the content.
     * $post_id This parameter is used if the method has been called outside the loop.
     * @return string The content with applied the autolinks.
     * 
     */
    public function add_autolinks($content, $check_query = true, $post_type = '', $post_id = false)
    {

        //Verify that we are inside a post, page or cpt
        if ($check_query) {
            if ( ! is_singular() or is_attachment() or is_feed()) {
                return $content;
            }
        }

        //If the $post_id is not set means that we are in the loop and can be retrieved with get_the_ID()
        if ($post_id === false) {
            $this->post_id = get_the_ID();
        } else {
            $this->post_id = $post_id;
        }

        //Get the permalink
        $post_permalink = get_permalink($this->post_id);

        /*
         * Verify with the "Enable Autolinks" post meta data or (if the meta data is not present) verify with the
         * "Enable Autolinks" option if the autolinks should be applied to this post.
         */
        $enable_autolinks = get_post_meta($this->post_id, '_daextam_enable_autolinks', true);
        if (strlen(trim($enable_autolinks)) === 0) {
            $enable_autolinks = get_option($this->get('slug') . '_advanced_enable_autolinks');
        }
        if (intval($enable_autolinks, 10) === 0) {
        	$this->number_of_replacements = 0;
            return $content;
        }

        //Protect the tags and the commented HTML with the protected blocks
        $content = $this->apply_protected_blocks($content);

        //Get the maximum number of autolinks allowed per post
        $this->max_number_autolinks_per_post = $this->get_max_number_autolinks_per_post($this->post_id);

        //Save the "Same URL Limit" as a class property
        $this->same_url_limit = intval(get_option($this->get('slug') . '_advanced_same_url_limit'), 10);

        //Get an array with the autolinks from the db table
        global $wpdb;
        $table_name = $wpdb->prefix . $this->get('slug') . "_autolink";
        $sql        = "SELECT * FROM $table_name ORDER BY priority DESC";
        $autolinks  = $wpdb->get_results($sql, ARRAY_A);

        /*
         * To avoid additional database requests for each autolink in preg_replace_callback_2() save the data of the
         * autolink in an array that uses the "autolink_id" as its index.
         */
        $this->autolinks_ca = $this->save_autolinks_in_custom_array($autolinks);

        //Apply the Random Prioritization if enabled
        if (intval(get_option($this->get('slug') . '_advanced_random_prioritization'), 10) === 1) {
            $autolinks = $this->apply_random_prioritization($autolinks, $this->post_id);
        }

        //Iterate through all the defined autolinks
        foreach ($autolinks as $key => $autolink) {

            //Save this autolink as a class property
            $this->parsed_autolink = $autolink;

            /*
             * If $post_type is not empty means that we are adding the autolinks through the back-end, in this case set
             * the $this->parsed_post_type property with the $post_type variable.
             *
             * If $post_type is empty means that we are in the loop and the post type can be retrieved with the
             * get_post_type() function.
             */
            if ($post_type !== '') {
                $this->parsed_post_type = $post_type;
            } else {
                $this->parsed_post_type = get_post_type();
            }

            /*
             * If the "Ignore Self Autolinks" option is set to true, do not apply the autolinks that have, as a target,
             * the post where they should be applied.
             */
            if (intval(get_option($this->get('slug') . '_advanced_ignore_self_autolinks'), 10) === 1) {
                if ($autolink['url'] === $post_permalink) {
                    continue;
                }
            }

            //Get the list of post types where the autolinks should be applied
            $post_types_a = maybe_unserialize($autolink['post_types']);

            //if $post_types_a is not an array fill $post_types_a with the posts available in the website
            if ( ! is_array($post_types_a)) {
                $post_types_a = $this->get_post_types_with_ui();
            }

            //Verify the post type
            if (in_array($this->parsed_post_type, $post_types_a) === false) {
                continue;
            }

            /*
             * If the term group is not set:
             *
             * - Check if the post is compliant by verifying categories and tags
             *
             * If the term group is set:
             *
             * - Check if the post is compliant by verifying the term group
             */
            if (intval($autolink['term_group_id'], 10) === 0) {

                /*
                 * Verify categories and tags only in the "post" post type or in all the posts. This verification is based
                 * on the value of the $categories_and_tags_verification option.
                 *
                 * - If $categories_and_tags_verification is equal to "any" verify the presence of the selected categories
                 * and tags in any post type.
                 * - If $categories_and_tags_verification is equal to "post" verify the presence of the selected categories
                 * and tags only in the "post" post type.
                 */
                $categories_and_tags_verification =  get_option($this->get('slug') . "_advanced_categories_and_tags_verification");
                if (($categories_and_tags_verification === 'any' or get_post_type() === 'post') and
                    ( ! $this->is_compliant_with_categories($this->post_id, $autolink) or
                      ! $this->is_compliant_with_tags($this->post_id, $autolink))) {
                    continue;
                }

            }else{

                //Do not proceed with the application of the autolink if this post is not compliant with the term group
                if ( ! $this->is_compliant_with_term_group($this->post_id, $autolink)) {
                    continue;
                }

            }

            //Get the max number of autolinks per keyword
            $max_number_autolinks_per_keyword = $autolink['limit'];

            //Apply a case sensitive search if the case_sensitive_flag is set to True
            if ($autolink['case_sensitive_search']) {
                $modifier = 'u';//enable unicode modifier
            } else {
                $modifier = 'iu';//enable case insensitive and unicode modifier
            }

            //Find the left boundary
            switch ($autolink['left_boundary']) {
                case 0:
                    $left_boundary = '\b';
                    break;

                case 1:
                    $left_boundary = ' ';
                    break;

                case 2:
                    $left_boundary = ',';
                    break;

                case 3:
                    $left_boundary = '\.';
                    break;

                case 4:
                    $left_boundary = '';
                    break;
            }

            //Find the right boundary
            switch ($autolink['right_boundary']) {
                case 0:
                    $right_boundary = '\b';
                    break;

                case 1:
                    $right_boundary = ' ';
                    break;

                case 2:
                    $right_boundary = ',';
                    break;

                case 3:
                    $right_boundary = '\.';
                    break;

                case 4:
                    $right_boundary = '';
                    break;
            }

            //escape regex characters and the '/' regex delimiter
            $autolink_keyword        = preg_quote($autolink['keyword'], '/');
            $autolink_keyword_before = preg_quote($autolink['keyword_before'], '/');
            $autolink_keyword_after  = preg_quote($autolink['keyword_after'], '/');

            /*
             * Step 1: "The creation of temporary identifiers of the substitutions"
             *
             * Replaces all the matches with the [al]ID[/al] string, where the ID is the identifier of the substitution.
             * The ID is also used as the index of the $this->autolink_a temporary array used to store information about
             * all the substutions. This array will be later used in "Step 2" to replace the [al]ID[/al] string with the
             * actual links.
             */
            $content = preg_replace_callback(
                '/(' . $autolink_keyword_before . ')(' . ($left_boundary) . ')(' . $autolink_keyword . ')(' . ($right_boundary) . ')(' . $autolink_keyword_after . ')/' . $modifier,
                array($this, 'preg_replace_callback_1'),
                $content,
                $max_number_autolinks_per_keyword);

        }

        /*
         * Step 2: "The replacement of the temporary string [ail]ID[/ail]"
         *
         * Replaces the [al]ID[/al] matches found in the $content with the actual links by using the $this->autolink_a
         * array to find the identifier of the substitutions and by retrieving in the db table "autolinks" (with the
         * "autolink_id") additional information about the substitution.
         */
        $content = preg_replace_callback(
            '/\[al\](\d+)\[\/al\]/',
            array($this, 'preg_replace_callback_2'),
            $content,
            -1,
            $this->number_of_replacements);

        //Remove the protected blocks
        $content = $this->remove_protected_blocks($content);

        //Reset the id of the autolink
        $this->autolink_id = 0;

        //Reset the array that includes the data of the autolinks already applied
        $this->autolink_a = array();

        return $content;

    }

    /*
     * Replaces the following elements with [pr]ID[/pr]:
     *
     * - Protected Gutenberg Blocks
     * - Protected Gutenberg Custom Blocks
     * - Protected Gutenberg Custom Void Blocks
     * - The sections enclosed in HTML comments
     * - The Protected Tags
     * 
     * The replaced tags and URLs are saved in the property $pr_a, an array with the ID used in the block as the index.
     * 
     * @param $content string The unprotected $content
     * @return string The $content with applied the protected block
     */
    private function apply_protected_blocks($content)
    {

        $this->pb_id = 0;
        $this->pb_a  = array();

        //Get the Gutenberg Protected Blocks
        $protected_gutenberg_blocks   = get_option($this->get('slug') . '_advanced_protected_gutenberg_blocks');
        $protected_gutenberg_blocks_a = maybe_unserialize($protected_gutenberg_blocks);
        if ( ! is_array($protected_gutenberg_blocks_a)) {
            $protected_gutenberg_blocks_a = array();
        }

        //Get the Protected Gutenberg Custom Blocks
        $protected_gutenberg_custom_blocks   = get_option($this->get('slug') . '_advanced_protected_gutenberg_custom_blocks');
        $protected_gutenberg_custom_blocks_a = array_filter(explode(',',
            str_replace(' ', '', trim($protected_gutenberg_custom_blocks))));

        //Get the Protected Gutenberg Custom Void Blocks
        $protected_gutenberg_custom_void_blocks   = get_option($this->get('slug') . '_advanced_protected_gutenberg_custom_void_blocks');
        $protected_gutenberg_custom_void_blocks_a = array_filter(explode(',',
            str_replace(' ', '', trim($protected_gutenberg_custom_void_blocks))));

        $protected_gutenberg_blocks_comprehensive_list_a = array_merge($protected_gutenberg_blocks_a,
            $protected_gutenberg_custom_blocks_a, $protected_gutenberg_custom_void_blocks_a);

        if (is_array($protected_gutenberg_blocks_comprehensive_list_a)) {

            foreach ($protected_gutenberg_blocks_comprehensive_list_a as $key => $block) {

                //Non-Void Blocks
                if ($block === 'paragraph' or
                    $block === 'image' or
                    $block === 'heading' or
                    $block === 'gallery' or
                    $block === 'list' or
                    $block === 'quote' or
                    $block === 'audio' or
                    $block === 'cover-image' or
                    $block === 'subhead' or
                    $block === 'video' or
                    $block === 'code' or
                    $block === 'preformatted' or
                    $block === 'pullquote' or
                    $block === 'table' or
                    $block === 'verse' or
                    $block === 'button' or
                    $block === 'columns' or
                    $block === 'more' or
                    $block === 'nextpage' or
                    $block === 'separator' or
                    $block === 'spacer' or
                    $block === 'text-columns' or
                    $block === 'shortcode' or
                    $block === 'embed' or
                    $block === 'core-embed/twitter' or
                    $block === 'core-embed/youtube' or
                    $block === 'core-embed/facebook' or
                    $block === 'core-embed/instagram' or
                    $block === 'core-embed/wordpress' or
                    $block === 'core-embed/soundcloud' or
                    $block === 'core-embed/spotify' or
                    $block === 'core-embed/flickr' or
                    $block === 'core-embed/vimeo' or
                    $block === 'core-embed/animoto' or
                    $block === 'core-embed/cloudup' or
                    $block === 'core-embed/collegehumor' or
                    $block === 'core-embed/dailymotion' or
                    $block === 'core-embed/funnyordie' or
                    $block === 'core-embed/hulu' or
                    $block === 'core-embed/imgur' or
                    $block === 'core-embed/issuu' or
                    $block === 'core-embed/kickstarter' or
                    $block === 'core-embed/meetup-com' or
                    $block === 'core-embed/mixcloud' or
                    $block === 'core-embed/photobucket' or
                    $block === 'core-embed/polldaddy' or
                    $block === 'core-embed/reddit' or
                    $block === 'core-embed/reverbnation' or
                    $block === 'core-embed/screencast' or
                    $block === 'core-embed/scribd' or
                    $block === 'core-embed/slideshare' or
                    $block === 'core-embed/smugmug' or
                    $block === 'core-embed/speaker' or
                    $block === 'core-embed/ted' or
                    $block === 'core-embed/tumblr' or
                    $block === 'core-embed/videopress' or
                    $block === 'core-embed/wordpress-tv' or
                    in_array($block, $protected_gutenberg_custom_blocks_a)
                ) {

                    //escape regex characters and the '/' regex delimiter
                    $block = preg_quote($block, '/');

                    //Non-Void Blocks Regex
                    $content = preg_replace_callback(
                        '/
                    <!--\s+(wp:' . $block . ').*?-->        #1 Gutenberg Block Start
                    .*?                                     #2 Gutenberg Content
                    <!--\s+\/\1\s+-->                       #3 Gutenberg Block End
                    /ixs',
                        array($this, 'apply_single_protected_block'),
                        $content
                    );

                    //Void Blocks
                } elseif ($block === 'html' or
                          $block === 'categories' or
                          $block === 'latest-posts' or
                          in_array($block, $protected_gutenberg_custom_void_blocks_a)
                ) {

                    //escape regex characters and the '/' regex delimiter
                    $block = preg_quote($block, '/');

                    //Void Blocks Regex
                    $content = preg_replace_callback(
                        '/
                    <!--\s+wp:' . $block . '.*?\/-->        #1 Void Block
                    /ix',
                        array($this, 'apply_single_protected_block'),
                        $content
                    );

                }

            }

        }

        /*
         * Protect the commented sections, enclosed between <!-- and -->
         */
        $content = preg_replace_callback(
            '/
            <!--                                #1 Comment Start
            .*?                                 #2 Any character zero or more time with a lazy quantifier
            -->                                 #3 Comment End
            /ix',
            array($this, 'apply_single_protected_block'),
            $content
        );

        /*
         * Get the list of the protected tags from the "Protected Tags" option
         */
        $protected_tags   = get_option($this->get('slug') . '_advanced_protected_tags');
        $protected_tags_a = maybe_unserialize($protected_tags);

        if (is_array($protected_tags_a)) {

            foreach ($protected_tags_a as $key => $single_protected_tag) {

                /*
                 * Validate the tag. HTML elements all have names that only use
                 * characters in the range 0–9, a–z, and A–Z.
                 */
                if (preg_match('/^[0-9a-zA-Z]+$/', $single_protected_tag) === 1) {

                    //Make the tag lowercase
                    $single_protected_tag = strtolower($single_protected_tag);

                    //Apply different treatment if the tag is a void tag or a non-void tag.
                    if ($single_protected_tag == 'area' or
                        $single_protected_tag == 'base' or
                        $single_protected_tag == 'br' or
                        $single_protected_tag == 'col' or
                        $single_protected_tag == 'embed' or
                        $single_protected_tag == 'hr' or
                        $single_protected_tag == 'img' or
                        $single_protected_tag == 'input' or
                        $single_protected_tag == 'keygen' or
                        $single_protected_tag == 'link' or
                        $single_protected_tag == 'meta' or
                        $single_protected_tag == 'param' or
                        $single_protected_tag == 'source' or
                        $single_protected_tag == 'track' or
                        $single_protected_tag == 'wbr'
                    ) {

                        //Apply the protected block on void tags
                        $content = preg_replace_callback(
                            '/                                  
                            <                                   #1 Begin the start-tag
                            (' . $single_protected_tag . ')     #2 The tag name (captured for the backreference)
                            (\s+[^>]*)?                         #3 Match the rest of the start-tag
                            >                                   #4 End the start-tag
                            /ix',
                            array($this, 'apply_single_protected_block'),
                            $content
                        );

                    } else {

                        //Apply the protected block on non-void tags
                        $content = preg_replace_callback(
                            '/
                            <                                   #1 Begin the start-tag
                            (' . $single_protected_tag . ')     #2 The tag name (captured for the backreference)
                            (\s+[^>]*)?                         #3 Match the rest of the start-tag
                            >                                   #4 End the start-tag
                            .*?                                 #5 The element content (with the "s" modifier the dot matches also the new lines)
                            <\/\1\s*>                           #6 The end-tag with a backreference to the tag name (\1) and optional white-spaces before the closing >
                            /ixs',
                            array($this, 'apply_single_protected_block'),
                            $content
                        );

                    }

                }

            }

        }

        return $content;

    }

    /*
     * This method is used inside all the preg_replace_callback located in the apply_protected_blocks() method.
     * 
     * What it does is:
     *
     * 1 - Saves the match in the $pb_a array
     * 2 - Returns the protected block with the related identifier ([pb]ID[/pb])
     * 
     * @param $m An array with at index 0 the complete match and at index 1 the capture group.
     * @return string
     */
    private function apply_single_protected_block($m)
    {

        //save the match in the $pb_a array
        $this->pb_id++;
        $this->pb_a[$this->pb_id] = $m[0];

        //Replaces the portion of post with the protected block and the index of the $pb_a array as the identifier.
        return '[pb]' . $this->pb_id . '[/pb]';

    }

    /*
     * Replaces the block [pr]ID[/pr] with the related portion of post found in the $pb_a property.
     * 
     * @param $content string The $content with applied the protected block.
     * return string The unprotected content.
     */
    private function remove_protected_blocks($content)
    {

        $content = preg_replace_callback(
            '/\[pb\](\d+)\[\/pb\]/',
            array($this, 'preg_replace_callback_3'),
            $content
        );

        return $content;

    }

    /*
     * Callback of the preg_replace_callback() function.
     * 
     * This callback is used to avoid an anonymous function as a parameter of the preg_replace_callback() function for
     * PHP backward compatibility.
     * 
     * Look for uses of preg_replace_callback_1 to find which preg_replace_callback() function is actually using this
     * callback.
     */
    public function preg_replace_callback_1($m)
    {

        /*
         * Do not apply the replacement and return the matched string in the following cases:
         *
         * - If the max number of autolinks per post has been reached
         * - If the "Same URL Limit" has been reached
         */
        if ($this->max_number_autolinks_per_post === $this->autolink_id or
            $this->same_url_limit_reached()) {

            return $m[1] . $m[2] . $m[3] . $m[4] . $m[5];

        } else {

            /*
             * Increases the $autolink_id property and stores the information related to this autolink and match in the
             * $autolink_a property. These information will be later used to replace the temporary identifiers of the
             * autolinks with the related data, and also in this method to verify the "Same URL Limit" option.
             */
            $this->autolink_id++;
            $this->autolink_a[$this->autolink_id]['autolink_id']    = $this->parsed_autolink['autolink_id'];
            $this->autolink_a[$this->autolink_id]['url']            = $this->parsed_autolink['url'];
            $this->autolink_a[$this->autolink_id]['text']           = $m[3];
            $this->autolink_a[$this->autolink_id]['left_boundary']  = $m[2];
            $this->autolink_a[$this->autolink_id]['right_boundary'] = $m[4];
            $this->autolink_a[$this->autolink_id]['keyword_before'] = $m[1];
            $this->autolink_a[$this->autolink_id]['keyword_after']  = $m[5];

            //Replaces the match with the temporary identifier of the autolink
            return '[al]' . $this->autolink_id . '[/al]';

        }

    }

    /*
     * Callback of the preg_replace_callback() function
     * 
     * This callback is used to avoid an anonymous function as a parameter of the preg_replace_callback() function for
     * PHP backward compatibility.
     * 
     * Look for uses of preg_replace_callback_2 to find which preg_replace_callback() function is actually using this
     * callback.
     */
    public function preg_replace_callback_2($m)
    {

        /*
         * Find the related text of the link from the $this->autolink_a multidimensional array by using the match as
         * the index.
         */
        $link_text = $this->autolink_a[$m[1]]['text'];

        //Get the left and right boundaries
        $left_boundary  = $this->autolink_a[$m[1]]['left_boundary'];
        $right_boundary = $this->autolink_a[$m[1]]['right_boundary'];


        //Get the keyword_before and keyword_after
        $keyword_before = $this->autolink_a[$m[1]]['keyword_before'];
        $keyword_after  = $this->autolink_a[$m[1]]['keyword_after'];

        //Get the autolink_id
        $autolink_id = $this->autolink_a[$m[1]]['autolink_id'];

        //Generates the title attribute HTML if the "title" field is not empty
        if (mb_strlen(trim($this->autolinks_ca[$autolink_id]['title'])) > 0) {
            $title_attribute = 'title="' . esc_attr(stripslashes($this->autolinks_ca[$autolink_id]['title'])) . '"';
        } else {
            $title_attribute = '';
        }

        //Get the "open_new_tab" value
        if (intval($this->autolinks_ca[$autolink_id]['open_new_tab'], 10) == 1) {
            $open_new_tab = 'target="_blank"';
        } else {
            $open_new_tab = 'target="_self"';
        }

        //Get the "use_nofollow" value
        if (intval($this->autolinks_ca[$autolink_id]['use_nofollow'], 10) == 1) {
            $use_nofollow = 'rel="nofollow"';
        } else {
            $use_nofollow = '';
        }

        //Return the actual link
        return $keyword_before . $left_boundary . '<a data-autolink-id="' . $autolink_id . '" ' . $open_new_tab . ' ' . $use_nofollow . ' href="' . esc_url($this->autolinks_ca[$autolink_id]['url']) . '" ' . $title_attribute . '>' . $link_text . '</a>' . $right_boundary . $keyword_after;

    }

    /*
     * Callback of the preg_replace_callback() function.
     * 
     * This callback is used to avoid an anonymous function as a parameter of the preg_replace_callback() function for
     * PHP backward compatibility.
     * 
     * Look for uses of preg_replace_callback_3 to find which preg_replace_callback() function is actually using this
     * callback.
     */
    public function preg_replace_callback_3($m)
    {

        /*
         * The presence of nested protected blocks is verified. If a protected block is inside the content of a
         * protected block the remove_protected_block() method is applied recursively until there are no protected
         * blocks.
         */
        $html           = $this->pb_a[$m[1]];
        $recursion_ends = false;

        do {

            /*
             * If there are no protected blocks in content of the protected block end the recursion, otherwise apply
             * remove_protected_block() again.
             */
            if (preg_match('/\[pb\](\d+)\[\/pb\]/', $html) == 0) {
                $recursion_ends = true;
            } else {
                $html = $this->remove_protected_blocks($html);
            }

        } while ($recursion_ends === false);

        return $html;

    }

    /*
     * Returns true if there are exportable data or false if here are no exportable data.
     */
    public function exportable_data_exists()
    {

        $exportable_data = false;
        global $wpdb;

        $table_name  = $wpdb->prefix . $this->get('slug') . "_autolink";
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($total_items > 0) {
            $exportable_data = true;
        }

        return $exportable_data;

    }

    /*
     * Objects as a value are set to empty strings. This prevent to generate notices with the methods of the wpdb class.
     *
     * @param $data An array which includes objects that should be converted to a empty strings.
     * @return string An array where the objects have been replaced with empty strings.
     */
    public function replace_objects_with_empty_strings($data)
    {

        foreach ($data as $key => $value) {
            if (gettype($value) === 'object') {
                $data[$key] = '';
            }
        }

        return $data;

    }

    /*
     * Returns the maximum number of autolinks allowed per post by using the method explained below.
     *
     * If the "General Limit Mode" option is set to "Auto":
     *
     * The maximum number of autolinks per post is calculated based on the content length of this post divided for the
     * value of the "General Limit (Characters per Autolink)" option.
     *
     * If the "General Limit Mode" option is set to "Manual":
     *
     * The maximum number of autolinks per post is equal to the value of "General Limit (Amount)".
     *
     * @param $post_id int The post ID for which the maximum number autolinks per post should be calculated.
     * @return int The maximum number of autolinks allowed per post.
     */
    private function get_max_number_autolinks_per_post($post_id)
    {

        if (intval(get_option($this->get('slug') . '_advanced_general_limit_mode'), 10) === 0) {

            //Auto -----------------------------------------------------------------------------------------------------
            $post_obj                = get_post($post_id);
            $post_length             = mb_strlen($post_obj->post_content);
            $characters_per_autolink = intval(get_option($this->get('slug') . '_advanced_general_limit_characters_per_autolink'),
                10);

            return intval($post_length / $characters_per_autolink);

        } else {

            //Manual ---------------------------------------------------------------------------------------------------
            return intval(get_option($this->get('slug') . '_advanced_general_limit_amount'), 10);

        }

    }

    /*
     * Returns True if the post has the categories required by the autolink or if the autolink doesn't require any
     * specific category.
     *
     * @return Bool
     */
    private function is_compliant_with_categories($post_id, $autolink)
    {

        $autolink_categories_a = maybe_unserialize($autolink['categories']);
        $post_categories       = get_the_terms($post_id, 'category');
        $category_found        = false;

        //If no categories are specified return true
        if ( ! is_array($autolink_categories_a)) {
            return true;
        }

        /*
         * Do not proceed with the application of the autolink if in this post no categories included in
         * $autolink_categories_a are available.
         */
        foreach ($post_categories as $key => $post_single_category) {
            if (in_array($post_single_category->term_id, $autolink_categories_a)) {
                $category_found = true;
            }
        }

        if ($category_found) {
            return true;
        } else {
            return false;
        }

    }

    /*
     * Returns True if the post has the tags required by the autolink or if the autolink doesn't require any specific
     * tag.
     *
     * @return Bool
     */
    private function is_compliant_with_tags($post_id, $autolink)
    {

        $autolink_tags_a = maybe_unserialize($autolink['tags']);
        $post_tags       = get_the_terms($post_id, 'post_tag');
        $tag_found       = false;

        //If no tags are specified return true
        if ( ! is_array($autolink_tags_a)) {
            return true;
        }

        if ($post_tags !== false) {

            /*
             * Do not proceed with the application of the autolink if this post has at least one tag but no tags
             * included in $autolink_tags_a are available.
             */
            foreach ($post_tags as $key => $post_single_tag) {
                if (in_array($post_single_tag->term_id, $autolink_tags_a)) {
                    $tag_found = true;
                }
            }
            if ( ! $tag_found) {
                return false;
            }

        } else {

            //Do not proceed with the application of the autolink if this post has no tags associated
            return false;

        }

        return true;

    }

    /*
     * Verifies if the post includes at least one term included in the term group associated with the autolink.
     *
     * In the following conditions True is returned:
     *
     * - When a term group is not set
     * - When the post has at least one term present in the term group
     */
    private function is_compliant_with_term_group($post_id, $autolink)
    {

        $supported_terms = intval(get_option($this->get('slug') . '_advanced_supported_terms'), 10);

        global $wpdb;
        $table_name     = $wpdb->prefix . $this->get('slug') . "_term_group";
        $safe_sql       = $wpdb->prepare("SELECT * FROM $table_name WHERE term_group_id = %d ",
            $autolink['term_group_id']);
        $term_group_obj = $wpdb->get_row($safe_sql);

        if ($term_group_obj !== null) {

            for ($i = 1; $i <= $supported_terms; $i++) {

                $post_type = $term_group_obj->{'post_type_' . $i};
                $taxonomy  = $term_group_obj->{'taxonomy_' . $i};
                $term      = $term_group_obj->{'term_' . $i};

                //Verify post type, taxonomy and term as specified in the term group
                if ($post_type === $this->parsed_post_type and has_term($term, $taxonomy, $post_id)) {
                    return true;
                }

            }

            return false;

        }

        return true;

    }

    /*
     * Remove the HTML comment ( comment enclosed between <!-- and --> )
     *
     * @param $content The HTML with the comments
     * @return string The HTML without the comments
     */
    public function remove_html_comments($content)
    {

        $content = preg_replace(
            '/
            <!--                                #1 Comment Start
            .*?                                 #2 Any character zero or more time with a lazy quantifier
            -->                                 #3 Comment End
            /ix',
            '',
            $content
        );

        return $content;

    }

    /*
     * Remove the script tags
     *
     * @param $content The HTML with the script tags
     * @return string The HTML without the script tags
     */
    public function remove_script_tags($content)
    {

        $content = preg_replace(
            '/
            <                                   #1 Begin the start-tag
            script                              #2 The script tag name
            (\s+[^>]*)?                         #3 Match the rest of the start-tag
            >                                   #4 End the start-tag
            .*?                                 #5 The element content ( with the "s" modifier the dot matches also the new lines )
            <\/script\s*>                       #6 The script end-tag with optional white-spaces before the closing >
            /ixs',
            '',
            $content
        );

        return $content;

    }

    /*
     * Get the number of records available in the "statistic" db table
     *
     * @return int The number of records in the "statistic" db table
     */
    public function number_of_records_in_statistic()
    {

        global $wpdb;
        $table_name  = $wpdb->prefix . $this->get('slug') . "_statistic";
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        return $total_items;

    }

    /*
     * If the number of times that the parsed autolink ($this->parsed_autolink['url']) is present in the array that
     * includes the data of the autolinks already applied as temporary identifiers ($this->autolink_a) is equal or
     * higher than the limit estabilished with the "Same URL Limit" option ($this->same_url_limit) True is returned,
     * otherwise False is returned.
     *
     * @return Bool
     */
    public function same_url_limit_reached()
    {

        $counter = 0;

        foreach ($this->autolink_a as $key => $value) {
            if ($value['url'] === $this->parsed_autolink['url']) {
                $counter++;
            }
        }

        if ($counter >= $this->same_url_limit) {
            return true;
        } else {
            return false;
        }

    }

    /*
     * Applies a random order (based on the hash of the post_id and autolink_id) to the autolinks that have the same
     * priority. This ensures a better distribution of the autolinks.
     *
     * @param $autolink Array
     * @param $post_id Int
     * @return Array
     */
    public function apply_random_prioritization($autolinks, $post_id)
    {

        //Initialize variables
        $autolinks_rp1 = array();
        $autolinks_rp2 = array();

        //Move the autolinks array in the new $autolinks_rp1 array, which uses the priority value as its index
        foreach ($autolinks as $key => $autolink) {

            $autolinks_rp1[$autolink['priority']][] = $autolink;

        }

        /*
         * Apply a random order (based on the hash of the post_id and autolink_id) to the autolinks that have the same
         * priority.
         */
        foreach ($autolinks_rp1 as $key => $autolinks_a) {

            /*
             * In each autolink create the new "hash" field which include an hash value based on the post_id and on the
             * autolink_id.
             */
            foreach ($autolinks_a as $key2 => $autolink) {

                /*
                 * Create the hased value. Note that the "-" character is used to avoid situations where the same input
                 * is provided to the md5() function.
                 *
                 * Without the "-" character for example with:
                 *
                 * $post_id = 12 and $autolink['autolink_id'] = 34
                 *
                 * We provide the same input of:
                 * 
                 * $post_id = 123 and $autolink['autolink_id'] = 4
                 *
                 * etc.
                 */
                $hash = hexdec(md5($post_id . '-' . $autolink['autolink_id']));

                /*
                 * Convert all the non-digits to the character "1", this makes the comparison performed in the usort
                 * callback possible.
                 */
                $autolink['hash']   = preg_replace('/\D/', '1', $hash, -1, $replacement_done);
                $autolinks_a[$key2] = $autolink;

            }

            //Sort $autolinks_a based on the new value of the "hash" field
            usort($autolinks_a, function ($a, $b) {

                return $b['hash'] - $a['hash'];

            });

            $autolinks_rp1[$key] = $autolinks_a;

        }

        /*
         * Move the autolinks in the new $autolinks_rp2 array, which is structured like the original array, where the
         * value of the priority field is stored in the autolink and it's not used as the index of the array that
         * includes all the autolinks with the same priority.
         */
        foreach ($autolinks_rp1 as $key => $autolinks_a) {

            for ($t = 0; $t < (count($autolinks_a)); $t++) {

                $autolink        = $autolinks_a[$t];
                $autolinks_rp2[] = $autolink;

            }

        }

        return $autolinks_rp2;

    }

    /*
     * To avoid additional database requests for each autolink in preg_replace_callback_2() save the data of the
     * autolink in an array that uses the "autolink_id" as its index.
     *
     * @param $autolinks Array
     * @return Array
     */
    public function save_autolinks_in_custom_array($autolinks)
    {

        $autolinks_ca = array();

        foreach ($autolinks as $key => $autolink) {

            $autolinks_ca[$autolink['autolink_id']] = $autolink;

        }

        return $autolinks_ca;

    }

    /*
     * Given the Autolink ID the Autolink Object is returned.
     *
     * @param $autolink_id Int
     * @return Object
     */
    public function get_autolink_object($autolink_id)
    {

        global $wpdb;
        $table_name   = $wpdb->prefix . $this->get('slug') . "_autolink";
        $safe_sql     = $wpdb->prepare("SELECT * FROM $table_name WHERE autolink_id = %d ", $autolink_id);
        $autolink_obj = $wpdb->get_row($safe_sql);

        return $autolink_obj;

    }

    /*
     * Get an array with the post types with UI except the attachment post type.
     *
     * @return Array
     */
    public function get_post_types_with_ui()
    {

        //Get all the post types with UI
        $args               = array(
            'public'  => true,
            'show_ui' => true
        );
        $post_types_with_ui = get_post_types($args);

        //Remove the attachment post type
        unset($post_types_with_ui['attachment']);

        //Replace the associative index with a numeric index
        $temp_array = array();
        foreach ($post_types_with_ui as $key => $value) {
            $temp_array[] = $value;
        }
        $post_types_with_ui = $temp_array;

        return $post_types_with_ui;

    }

    /*
     * Returns true if the category with the specified $category_id exists.
     *
     * @param $category_id Int
     * @return bool
     */
    public function category_exists($category_id)
    {

        global $wpdb;

        $table_name  = $wpdb->prefix . $this->get('slug') . "_category";
        $safe_sql    = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE category_id = %d", $category_id);
        $total_items = $wpdb->get_var($safe_sql);

        if ($total_items > 0) {
            return true;
        } else {
            return false;
        }

    }

    /*
     * Returns true if one or more autolinks are using the specified category.
     *
     * @param $category_id Int
     * @return bool
     */
    public function category_is_used($category_id)
    {

        global $wpdb;

        $table_name  = $wpdb->prefix . $this->get('slug') . "_autolink";
        $safe_sql    = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE category_id = %d", $category_id);
        $total_items = $wpdb->get_var($safe_sql);

        if ($total_items > 0) {
            return true;
        } else {
            return false;
        }

    }

    /*
     * Returns true if the term group with the specified $term_group_id exists.
     *
     * @param $term_group_id Int
     * @return bool
     */
    public function term_group_exists($term_group_id)
    {

        global $wpdb;

        $table_name  = $wpdb->prefix . $this->get('slug') . "_term_group";
        $safe_sql    = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE term_group_id = %d", $term_group_id);
        $total_items = $wpdb->get_var($safe_sql);

        if ($total_items > 0) {
            return true;
        } else {
            return false;
        }

    }

    /*
     * Returns true if one or more autolinks are using the specified term group.
     *
     * @param $term_group_id Int
     * @return bool
     */
    public function term_group_is_used($term_group_id)
    {

        global $wpdb;

        $table_name  = $wpdb->prefix . $this->get('slug') . "_autolink";
        $safe_sql    = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE term_group_id = %d", $term_group_id);
        $total_items = $wpdb->get_var($safe_sql);

        if ($total_items > 0) {
            return true;
        } else {
            return false;
        }

    }

    /*
     * Given the category ID the category name is returned.
     *
     * @param $category_id Int
     * @return String
     */
    public function get_category_name($category_id)
    {

        if (intval($category_id, 10) === 0) {
            return esc_html__('None', 'daext-autolinks-manager');
        }

        global $wpdb;
        $table_name   = $wpdb->prefix . $this->get('slug') . "_category";
        $safe_sql     = $wpdb->prepare("SELECT * FROM $table_name WHERE category_id = %d ", $category_id);
        $category_obj = $wpdb->get_row($safe_sql);

        return $category_obj->name;

    }

    /*
     * Fires after a term is deleted from the database and the cache is cleaned.
     *
     * The following tasks are performed:
     *
     * Part 1 - Deletes the $term_id found in the categories field of the autolinks
     * Part 2 - Deletes the $term_id found in the tags field of the autolinks
     * Part 3 - Deletes the $term_id found in the 50 term_[n] fields of the term groups
     */
    public function delete_term_action($term_id, $term_taxonomy_id, $taxonomy_slug)
    {

        //Part 1-2 -------------------------------------------------------------------------------------------------------

        global $wpdb;
        $table_name = $wpdb->prefix . $this->get('slug') . "_autolink";
        $autolink_a = $wpdb->get_results("SELECT * FROM $table_name ORDER BY autolink_id ASC", ARRAY_A);

        if ($autolink_a !== null and count($autolink_a) > 0) {

            foreach ($autolink_a as $key1 => $autolink) {

                //Delete the term in the categories field of the autolinks
                $category_term_a = maybe_unserialize($autolink['categories']);
                if (is_array($category_term_a) and count($category_term_a) > 0) {
                    foreach ($category_term_a as $key2 => $category_term) {
                        if (intval($category_term, 10) === $term_id) {
                            unset($category_term_a[$key2]);
                        }
                    }
                }
                $category_term_a_serialized = maybe_serialize($category_term_a);

                //Delete the term in the tags field of the autolinks
                $tag_term_a = maybe_unserialize($autolink['tags']);
                if (is_array($tag_term_a) and count($tag_term_a) > 0) {
                    foreach ($tag_term_a as $key2 => $tag_term) {
                        if (intval($tag_term, 10) === $term_id) {
                            unset($tag_term_a[$key2]);
                        }
                    }
                }
                $tag_term_a_serialized = maybe_serialize($tag_term_a);

                //Update the record of the database if $categories or $tags are changed
                if ($autolink['categories'] !== $category_term_a_serialized or
                    $autolink['tags'] !== $tag_term_a_serialized) {

                    $safe_sql = $wpdb->prepare("UPDATE $table_name SET 
                        categories = %s,
                        tags = %s
                        WHERE autolink_id = %d",
                        $category_term_a_serialized,
                        $tag_term_a_serialized,
                        $autolink['autolink_id']);

                    $wpdb->query($safe_sql);

                }

            }


        }

        //Part 3 -------------------------------------------------------------------------------------------------------

        //Delete the term in all the 50 term_[n] field of the term groups
        $table_name   = $wpdb->prefix . $this->get('slug') . "_term_group";
        $term_group_a = $wpdb->get_results("SELECT * FROM $table_name ORDER BY term_group_id ASC", ARRAY_A);

        if ($term_group_a !== null and count($term_group_a) > 0) {

            foreach ($term_group_a as $key => $term_group) {

                $no_terms = true;
                for ($i = 1; $i <= 50; $i++) {

                    if (intval($term_group['term_' . $i], 10) === $term_id) {
                        $term_group['post_type_' . $i] = '';
                        $term_group['taxonomy_' . $i]  = '';
                        $term_group['term_' . $i]      = 0;
                    }

                    if (intval($term_group['term_' . $i], 10) !== 0) {
                        $no_terms = false;
                    }

                }

                /*
                 * If all the terms of the term group are empty delete the term group and reset the association between
                 * autolinks and this term group. If there are terms in the term group update the term group.
                 */
                if ($no_terms) {

                    //Delete the term group
                    $safe_sql     = $wpdb->prepare("DELETE FROM $table_name WHERE term_group_id = %d ",
                        $term_group['term_group_id']);
                    $query_result = $wpdb->query($safe_sql);

                    //If the term group is used reset the association between the autolinks and this term group
                    if ($this->term_group_is_used($term_group['term_group_id'])) {

                        //reset the association between the autolinks and this term group
                        $safe_sql = $wpdb->prepare("UPDATE $table_name SET 
                                    term_group_id = 0,
                                    WHERE term_group_id = %d",
                            $term_group['term_group_id']);

                    }

                } else {

                    //Update the term group

                    $query_part = '';
                    for ($i = 1; $i <= 50; $i++) {
                        $query_part .= 'post_type_' . $i . ' = %s,';
                        $query_part .= 'taxonomy_' . $i . ' = %s,';
                        $query_part .= 'term_' . $i . ' = %d';
                        if ($i !== 50) {
                            $query_part .= ',';
                        }
                    }

                    //update the database
                    global $wpdb;
                    $table_name = $wpdb->prefix . $this->get('slug') . "_term_group";
                    $safe_sql   = $wpdb->prepare("UPDATE $table_name SET
                        $query_part
                        WHERE term_group_id = %d",
                        $term_group["post_type_1"], $term_group["taxonomy_1"], $term_group["term_1"],
                        $term_group["post_type_2"], $term_group["taxonomy_2"], $term_group["term_2"],
                        $term_group["post_type_3"], $term_group["taxonomy_3"], $term_group["term_3"],
                        $term_group["post_type_4"], $term_group["taxonomy_4"], $term_group["term_4"],
                        $term_group["post_type_5"], $term_group["taxonomy_5"], $term_group["term_5"],
                        $term_group["post_type_6"], $term_group["taxonomy_6"], $term_group["term_6"],
                        $term_group["post_type_7"], $term_group["taxonomy_7"], $term_group["term_7"],
                        $term_group["post_type_8"], $term_group["taxonomy_8"], $term_group["term_8"],
                        $term_group["post_type_9"], $term_group["taxonomy_9"], $term_group["term_9"],
                        $term_group["post_type_10"], $term_group["taxonomy_10"], $term_group["term_10"],
                        $term_group["post_type_11"], $term_group["taxonomy_11"], $term_group["term_11"],
                        $term_group["post_type_12"], $term_group["taxonomy_12"], $term_group["term_12"],
                        $term_group["post_type_13"], $term_group["taxonomy_13"], $term_group["term_13"],
                        $term_group["post_type_14"], $term_group["taxonomy_14"], $term_group["term_14"],
                        $term_group["post_type_15"], $term_group["taxonomy_15"], $term_group["term_15"],
                        $term_group["post_type_16"], $term_group["taxonomy_16"], $term_group["term_16"],
                        $term_group["post_type_17"], $term_group["taxonomy_17"], $term_group["term_17"],
                        $term_group["post_type_18"], $term_group["taxonomy_18"], $term_group["term_18"],
                        $term_group["post_type_19"], $term_group["taxonomy_19"], $term_group["term_19"],
                        $term_group["post_type_20"], $term_group["taxonomy_20"], $term_group["term_20"],
                        $term_group["post_type_21"], $term_group["taxonomy_21"], $term_group["term_21"],
                        $term_group["post_type_22"], $term_group["taxonomy_22"], $term_group["term_22"],
                        $term_group["post_type_23"], $term_group["taxonomy_23"], $term_group["term_23"],
                        $term_group["post_type_24"], $term_group["taxonomy_24"], $term_group["term_24"],
                        $term_group["post_type_25"], $term_group["taxonomy_25"], $term_group["term_25"],
                        $term_group["post_type_26"], $term_group["taxonomy_26"], $term_group["term_26"],
                        $term_group["post_type_27"], $term_group["taxonomy_27"], $term_group["term_27"],
                        $term_group["post_type_28"], $term_group["taxonomy_28"], $term_group["term_28"],
                        $term_group["post_type_29"], $term_group["taxonomy_29"], $term_group["term_29"],
                        $term_group["post_type_30"], $term_group["taxonomy_30"], $term_group["term_30"],
                        $term_group["post_type_31"], $term_group["taxonomy_31"], $term_group["term_31"],
                        $term_group["post_type_32"], $term_group["taxonomy_32"], $term_group["term_32"],
                        $term_group["post_type_33"], $term_group["taxonomy_33"], $term_group["term_33"],
                        $term_group["post_type_34"], $term_group["taxonomy_34"], $term_group["term_34"],
                        $term_group["post_type_35"], $term_group["taxonomy_35"], $term_group["term_35"],
                        $term_group["post_type_36"], $term_group["taxonomy_36"], $term_group["term_36"],
                        $term_group["post_type_37"], $term_group["taxonomy_37"], $term_group["term_37"],
                        $term_group["post_type_38"], $term_group["taxonomy_38"], $term_group["term_38"],
                        $term_group["post_type_39"], $term_group["taxonomy_39"], $term_group["term_39"],
                        $term_group["post_type_40"], $term_group["taxonomy_40"], $term_group["term_40"],
                        $term_group["post_type_41"], $term_group["taxonomy_41"], $term_group["term_41"],
                        $term_group["post_type_42"], $term_group["taxonomy_42"], $term_group["term_42"],
                        $term_group["post_type_43"], $term_group["taxonomy_43"], $term_group["term_43"],
                        $term_group["post_type_44"], $term_group["taxonomy_44"], $term_group["term_44"],
                        $term_group["post_type_45"], $term_group["taxonomy_45"], $term_group["term_45"],
                        $term_group["post_type_46"], $term_group["taxonomy_46"], $term_group["term_46"],
                        $term_group["post_type_47"], $term_group["taxonomy_47"], $term_group["term_47"],
                        $term_group["post_type_48"], $term_group["taxonomy_48"], $term_group["term_48"],
                        $term_group["post_type_49"], $term_group["taxonomy_49"], $term_group["term_49"],
                        $term_group["post_type_50"], $term_group["taxonomy_50"], $term_group["term_50"],
                        $term_group['term_group_id']);

                    $query_result = $wpdb->query($safe_sql);

                }

            }

        }

    }

    /*
     * If $needle is present in the $haystack array echos 'selected="selected"'.
     *
     * @param $haystack Array
     * @param $needle String
     */
    public function selected_array($array, $needle)
    {

        if (is_array($array) and in_array($needle, $array)) {
            return 'selected="selected"';
        }

    }

    /*
     * Set the PHP "Max Execution Time" and "Memory Limit" based on the values defined in the options.
     */
    public function set_met_and_ml(){

        /*
         * Set the custom "Max Execution Time Value" defined in the options if the 'Set Max Execution Time' option is
         * set to "Yes".
         */
        if (intval(get_option($this->get('slug') . '_analysis_set_max_execution_time'), 10) === 1) {
            ini_set('max_execution_time',
                intval(get_option($this->get('slug') . '_analysis_max_execution_time_value'), 10));
        }

        /*
         * Set the custom "Memory Limit Value" (in megabytes) defined in the options if the 'Set Memory Limit' option is
         * set to "Yes".
         */
        if (intval(get_option($this->get('slug') . '_analysis_set_memory_limit'), 10) === 1) {
            ini_set('memory_limit', intval(get_option($this->get('slug') . "_analysis_memory_limit_value"), 10) . 'M');
        }

    }

}