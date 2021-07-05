<?php

namespace ILJ\Backend;

use  ILJ\Core\Options ;
use  ILJ\Helper\Help ;
use  ILJ\Type\KeywordList ;
use  ILJ\Database\Postmeta ;
use  ILJ\Helper\Capabilities ;
use  ILJ\Helper\IndexAsset ;
/**
 * Admin views
 *
 * Responsible for all view elements which are required on the backend
 *
 * @package ILJ\Backend
 *
 * @since 1.0.0
 */
class Editor
{
    const  ILJ_ADMINVIEW_NONCE = '_ilj_adminview_nonce' ;
    const  ILJ_ACTION_AFTER_KEYWORDS_UPDATE = 'ilj_after_keywords_update' ;
    const  ILJ_EDITOR_HANDLE = 'ilj_editor' ;
    const  ILJ_KEYWORDS_HANDLE = 'ilj_keywords' ;
    const  ILJ_IS_BLACKLISTED = 'ilj_is_blacklisted' ;
    const  ILJ_META_KEY_LIMITINCOMINGLINKS = 'ilj_limitincominglinks' ;
    const  ILJ_META_KEY_MAXINCOMINGLINKS = 'ilj_maxincominglinks' ;
    const  ILJ_META_KEY_BLACKLISTDEFINITION = 'ilj_blacklistdefinition' ;
    /**
     * Registers the keyword metabox on all public post types
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function addKeywordMetaBox()
    {
        foreach ( get_post_types( [
            'public' => true,
        ] ) as $type ) {
            add_meta_box(
                Postmeta::ILJ_META_KEY_LINKDEFINITION,
                __( 'Internal Links', 'internal-links' ),
                [ __CLASS__, 'renderKeywordMetaBox' ],
                $type,
                'side',
                'default'
            );
        }
    }
    
    /**
     * Renders the keyword metabox
     *
     * @since 1.0.0
     *
     * @param  WP_Post $post The post object of the current page
     * @return void
     */
    public static function renderKeywordMetaBox( \WP_Post $post )
    {
        $keyword_list = KeywordList::fromMeta( $post->ID, 'post', Postmeta::ILJ_META_KEY_LINKDEFINITION );
        $limitlinks = get_post_meta( $post->ID, Editor::ILJ_META_KEY_LIMITINCOMINGLINKS, true );
        $maxlimitlinks = get_post_meta( $post->ID, Editor::ILJ_META_KEY_MAXINCOMINGLINKS, true );
        $blacklist_keywords = KeywordList::fromMeta( $post->ID, 'post', Editor::ILJ_META_KEY_BLACKLISTDEFINITION );
        wp_nonce_field( basename( __FILE__ ), self::ILJ_ADMINVIEW_NONCE );
        echo  '<p>' ;
        echo  '<label for="' . Postmeta::ILJ_META_KEY_LINKDEFINITION . '_keys">' . __( "The keywords", 'internal-links' ) . ':</label>' ;
        echo  '<br />' ;
        echo  '<input type="text" name="' . Postmeta::ILJ_META_KEY_LINKDEFINITION . '_keys" value="' . $keyword_list->encoded() . '" size="30" />' ;
        echo  '<input type="text" name="' . Editor::ILJ_IS_BLACKLISTED . '" value="' . self::isBlacklisted( $post->ID, "post" ) . '" style="display:none" />' ;
        echo  '<input type="text" name="' . Editor::ILJ_META_KEY_BLACKLISTDEFINITION . '" value="' . $blacklist_keywords->encoded() . '" size="30" style="display:none" />' ;
        echo  '</p>' ;
    }
    
    /**
     * Responsible for saving keyword meta values and
     * stores limit linking settings for posts
     *
     * @since 1.0.0
     *
     * @param  int     $post_id The ID of the post
     * @param  WP_Post $post    The post object
     * @return void
     */
    public static function saveKeywordMeta( $post_id, \WP_Post $post )
    {
        if ( !isset( $_POST[self::ILJ_ADMINVIEW_NONCE] ) || !wp_verify_nonce( $_POST[self::ILJ_ADMINVIEW_NONCE], basename( __FILE__ ) ) ) {
            return $post_id;
        }
        $post_type = get_post_type_object( $post->post_type );
        if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
            return $post_id;
        }
        
        if ( array_key_exists( Editor::ILJ_META_KEY_BLACKLISTDEFINITION, $_POST ) ) {
            $input_blacklist = stripslashes( $_POST[Editor::ILJ_META_KEY_BLACKLISTDEFINITION] );
            $sanitized_blacklist_meta_value = sanitize_text_field( $input_blacklist );
            $keywordsblacklist = KeywordList::fromInput( $sanitized_blacklist_meta_value );
            update_post_meta( $post_id, Editor::ILJ_META_KEY_BLACKLISTDEFINITION, array_slice( $keywordsblacklist->getKeywords(), 0, 2 ) );
        }
        
        
        if ( array_key_exists( Editor::ILJ_IS_BLACKLISTED, $_POST ) && $_POST[Editor::ILJ_IS_BLACKLISTED] == true ) {
            self::addToBlacklist( $post_id, "post" );
        } else {
            self::removeFromBlacklist( $post_id, "post" );
        }
        
        
        if ( array_key_exists( Postmeta::ILJ_META_KEY_LINKDEFINITION . '_keys', $_POST ) ) {
            $input = stripslashes( $_POST[Postmeta::ILJ_META_KEY_LINKDEFINITION . '_keys'] );
            $sanitized_meta_value = sanitize_text_field( $input );
            $keywords = KeywordList::fromInput( $sanitized_meta_value );
            update_post_meta( $post_id, Postmeta::ILJ_META_KEY_LINKDEFINITION, $keywords->getKeywords() );
            /**
             * Fires after keyword meta got saved
             *
             * @since 1.0.0
             */
            do_action( self::ILJ_ACTION_AFTER_KEYWORDS_UPDATE, $keywords->getKeywords() );
        }
    
    }
    
    /**
     * Logic for adding the assets based on subscription
     *
     * @since 1.1.0
     *
     * @return void
     */
    public static function addAssets()
    {
        $required_role = 'administrator';
        if ( !current_user_can( $required_role ) ) {
            return;
        }
        global  $pagenow ;
        
        if ( in_array( $pagenow, [ 'post-new.php', 'post.php' ] ) ) {
            
            if ( !isset( $_GET['post_type'] ) ) {
                self::registerAssets();
                return;
            }
            
            $post_type = get_post_type_object( $_GET['post_type'] );
            if ( !$post_type || !$post_type->public ) {
                return;
            }
            self::registerAssets();
        }
    
    }
    
    /**
     * Registering the assets for editor frontend
     *
     * @since 1.1.0
     *
     * @return void
     */
    private static function registerAssets()
    {
        add_action(
            'admin_enqueue_scripts',
            function ( $hook ) {
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_register_script(
                Editor::ILJ_KEYWORDS_HANDLE,
                ILJ_URL . 'admin/js/ilj_keywords.js',
                [],
                ILJ_VERSION
            );
            wp_register_script(
                Editor::ILJ_EDITOR_HANDLE,
                ILJ_URL . 'admin/js/ilj_editor.js',
                [],
                ILJ_VERSION
            );
            wp_localize_script( Editor::ILJ_EDITOR_HANDLE, 'ilj_editor_translation', Editor::getTranslation() );
            wp_add_inline_script( Editor::ILJ_EDITOR_HANDLE, "const ilj_editor_basic_restriction = " . json_encode( Editor::getBasicRestrictions() ), 'before' );
            wp_enqueue_script(
                'ilj_tipso',
                ILJ_URL . 'admin/js/tipso.js',
                [],
                ILJ_VERSION
            );
            wp_enqueue_script( Editor::ILJ_KEYWORDS_HANDLE );
            wp_enqueue_script( Editor::ILJ_EDITOR_HANDLE );
            wp_enqueue_style(
                'ilj_tipso',
                ILJ_URL . 'admin/css/tipso.css',
                [],
                ILJ_VERSION
            );
            wp_enqueue_style(
                Editor::ILJ_EDITOR_HANDLE,
                ILJ_URL . 'admin/css/ilj_editor.css',
                [],
                ILJ_VERSION
            );
            wp_enqueue_style(
                'ilj_ui',
                ILJ_URL . 'admin/css/ilj_ui.css',
                [],
                ILJ_VERSION
            );
        },
            10,
            1
        );
    }
    
    /**
     * Returns the frontend translation
     *
     * @since 1.0.1
     *
     * @return array
     */
    public static function getTranslation()
    {
        $translation = [
            'add_keyword'                               => __( 'Add Keyword', 'internal-links' ),
            'placeholder_keyword'                       => __( 'Keyword', 'internal-links' ),
            'howto_case'                                => __( 'Keywords get used <strong>case insensitive</strong>', 'internal-links' ),
            'howto_keyword'                             => __( 'Separate multiple keywords by commas', 'internal-links' ),
            'howto_gap'                                 => __( 'Configure the gap dimension. It represents the number of keywords that appear between your other keywords dynamically. Learn more in our documentation:', 'internal-links' ) . '<br><strong><a target="_blank" rel="noopener" href="' . Help::getLinkUrl(
            'editor/',
            'gaps',
            'gap help',
            'editor'
        ) . '">' . __( 'Click here to open', 'internal-links' ) . '</a></strong>',
            'headline_gaps'                             => __( 'Keyword gaps', 'internal-links' ),
            'add_gap'                                   => __( 'Add gap', 'internal-links' ),
            'gap_type'                                  => __( 'Gap type', 'internal-links' ),
            'type_min'                                  => __( 'Minimum', 'internal-links' ),
            'type_exact'                                => __( 'Exact', 'internal-links' ),
            'type_max'                                  => __( 'Maximum', 'internal-links' ),
            'howto_gap_min'                             => __( 'Minimum amount of keywords within the gap. No upper limits.', 'internal-links' ),
            'howto_gap_exact'                           => __( 'Exact amount of keywords within the gap.', 'internal-links' ),
            'howto_gap_max'                             => __( 'Maximum amount of keywords within the gap.', 'internal-links' ),
            'insert_gaps'                               => __( 'Insert gaps between keywords', 'internal-links' ),
            'headline_configured_keywords'              => __( 'Configured keywords', 'internal-links' ),
            'message_keyword_exists'                    => __( 'This keyword already exists.', 'internal-links' ),
            'message_no_keyword'                        => __( 'No keyword defined.', 'internal-links' ),
            'message_length_not_valid'                  => __( 'Length of given keyword not valid.', 'internal-links' ),
            'message_multiple_placeholder'              => __( 'Multiple consecutive placeholders are not allowed.', 'internal-links' ),
            'no_keywords'                               => __( 'No keywords configured.', 'internal-links' ),
            'gap_hover_exact'                           => __( 'Exact keyword gap:', 'internal-links' ),
            'gap_hover_max'                             => __( 'Maximum keyword gap:', 'internal-links' ),
            'gap_hover_min'                             => __( 'Minimum keyword gap:', 'internal-links' ),
            'get_help'                                  => __( 'Get help', 'internal-links' ),
            'limit_incoming_links'                      => __( 'Limit incoming Links:', 'internal-links' ),
            'max_incoming_links'                        => __( 'Maximum incoming links:', 'internal-links' ),
            'blacklist_incoming_links'                  => __( 'Keywords, that don`t get linked in the current content:', 'internal-links' ),
            'message_limited_blacklist_keyword'         => __( 'With our Basic version, you can block 2 keywords from being linked.', 'internal-links' ),
            'message_limited_blacklist_keyword_upgrade' => sprintf( '&raquo; <a href="%s">', get_admin_url( null, 'admin.php?page=' . AdminMenu::ILJ_MENUPAGE_SLUG . '-pricing' ) ) . __( 'Upgrade to Pro and add unlimited keywords', 'internal-links' ) . '</a>',
            'headline_configured_keywords_blacklist'    => __( 'Configured keyword blacklist:', 'internal-links' ),
            'is_blacklisted'                            => __( 'Is on global blacklist:', 'internal-links' ),
        ];
        return $translation;
    }
    
    /**
     * Sets Basic version restrictions
     *
     * @version 1.2.15
     *
     * @return array
     */
    protected static function getBasicRestrictions()
    {
        $current_screen = get_current_screen();
        $basic_restrictions = array(
            'blacklist_limit' => 2,
            'is_active'       => true,
            'disable_title'   => 'class="pro-title"',
            'disable_setting' => 'class="pro-setting" disabled',
            'lock_icon'       => '<span class="dashicons dashicons-lock tip" title="' . __( 'This feature is part of the Pro version', 'internal-links' ) . '"></span>',
            'current_screen'  => $current_screen->post_type,
        );
        return $basic_restrictions;
    }
    
    /**
     * Checks if an asset is on the blacklist
     *
     * @since 1.2.15
     *
     * @param  int    $id   The asset ID
     * @param  string $type The asset type
     * @return bool True if blacklisted , false if not
     */
    public static function isBlacklisted( $id, $type )
    {
        
        if ( $type == "post" ) {
            $postBlacklist = Options::getOption( \ILJ\Core\Options\Blacklist::getKey() );
            $blacklisted = false;
            if ( is_array( $postBlacklist ) ) {
                if ( in_array( $id, $postBlacklist ) ) {
                    $blacklisted = true;
                }
            }
            return $blacklisted;
        }
        
        return false;
    }
    
    /**
     * Removes an asset from blacklist
     *
     * @since 1.2.15
     *
     * @param  int    $id   The asset id
     * @param  string $type The asset type
     * @return void
     */
    protected static function removeFromBlacklist( $id, $type )
    {
        $blacklist = [];
        if ( $type == "post" ) {
            $blacklist = Options::getOption( \ILJ\Core\Options\Blacklist::getKey() );
        }
        $blacklist = ( is_array( $blacklist ) ? $blacklist : [] );
        
        if ( ($key = array_search( $id, $blacklist )) !== false ) {
            unset( $blacklist[$key] );
        } else {
            return;
        }
        
        if ( $type == "post" ) {
            Options::setOption( \ILJ\Core\Options\Blacklist::getKey(), $blacklist );
        }
    }
    
    /**
     * Adds ID to Blacklist Option of post/terms
     *
     * @since 1.2.15
     *
     * @param  int    $id   The asset id
     * @param  string $type The asset type
     * @return void
     */
    protected static function addToBlacklist( $id, $type )
    {
        $blacklist = [];
        if ( $type == "post" ) {
            $blacklist = Options::getOption( \ILJ\Core\Options\Blacklist::getKey() );
        }
        $blacklist = ( is_array( $blacklist ) ? $blacklist : [] );
        if ( !in_array( $id, $blacklist ) ) {
            $blacklist[] = $id;
        }
        if ( $type == "post" ) {
            Options::setOption( \ILJ\Core\Options\Blacklist::getKey(), $blacklist );
        }
    }

}