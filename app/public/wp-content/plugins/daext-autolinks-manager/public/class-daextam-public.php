<?php

/*
 * This class should be used to work with the public side of wordpress.
 */

class Daextam_Public
{

    //general class properties
    protected static $instance = null;
    private $shared = null;

    private function __construct()
    {

        //assign an instance of the plugin info
        $this->shared = Daextam_Shared::get_instance();

        //write in front-end head
        add_action('wp_head', array($this, 'wr_public_head'));

        /*
         * Add the autolink on the content if the test mode option is not activated or if the the current user has the
         * 'manage_options' capability.
         */
        if (
            intval(get_option($this->shared->get('slug') . '_advanced_enable_test_mode'), 10) === 0 or
            current_user_can('manage_options')
        ) {
            add_filter('the_content', array($this->shared, 'add_autolinks'),
                intval(get_option($this->shared->get('slug') . '_advanced_filter_priority'), 10));
        }

	    /**
	     * Register specific meta fields to the Rest API
	     */
	    add_action( 'init', array($this, 'rest_api_register_meta'));

	    /*
	     * Add custom routes to the Rest API
	     */
	    add_action( 'rest_api_init', array($this, 'rest_api_register_route'));

    }

    /*
     * Creates an instance of this class.
     */
    public static function get_instance()
    {

        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;

    }

    public function wr_public_head()
    {

        //javascript variables
        echo '<script type="text/javascript">';
        echo 'var daextamAjaxUrl = "' . admin_url('admin-ajax.php') . '";';
        echo 'var daextamNonce = "' . wp_create_nonce("daextam") . '";';
        echo '</script>';

    }

	/*
	 * Register specific meta fields to the Rest API
	 */
	function rest_api_register_meta() {

		register_meta( 'post', '_daextam_enable_autolinks', array(
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'auth_callback' => function(){return true;}
		) );

	}

	/*
	 * Add custom routes to the Rest API
	 */
	function rest_api_register_route(){

		//Add the GET 'daext-autolinks-manager/v1/options' endpoint to the Rest API
		register_rest_route(
			'daext-autolinks-manager/v1', '/options', array(
				'methods'  => 'GET',
				'callback' => array($this, 'rest_api_daext_autolinks_manager_read_options_callback'),
				'permission_callback' => '__return_true'
			)
		);

	}

	/*
	 * Callback for the GET 'daext-autolinks-manager/v1/options' endpoint of the Rest API
	 */
	function rest_api_daext_autolinks_manager_read_options_callback( $data ) {

		//Check the capability
		if (!current_user_can('manage_options')) {
			return new WP_Error(
				'rest_read_error',
				esc_html__('Sorry, you are not allowed to view the Autolinks Manager options.', 'daext-autolinks-manager'),
				array('status' => 403)
			);
		}

		//Generate the response
		$response = [];
		foreach($this->shared->get('options') as $key => $value){
			$response[$key] = get_option($key);
		}

		//Prepare the response
		$response = new WP_REST_Response($response);

		return $response;

	}

}