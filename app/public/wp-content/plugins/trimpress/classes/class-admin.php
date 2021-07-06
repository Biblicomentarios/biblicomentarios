<?php
/**
 * The file that defines the admin class.
 *
 * @package    TrimPress
 * @subpackage TrimPress/classes
 */

// Define the plugin namespace.
Namespace TrimPress;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin class.
 *
 * @package    TrimPress
 * @subpackage TrimPress/classes
 */
class Admin {

	/**
	 * The class constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_submenu' ) );
    }
    
    /**
	 * Create the submenu page.
	 */
    public function create_submenu() {
        add_submenu_page(
            'options-general.php',
            __( 'TrimPress Settings', 'trimpress' ),
            'TrimPress',
            'manage_options',
            'trimpress',
            array( $this, 'admin_html' )
        );
    }
	
	/**
	 * Callback to render the submenu page markup.
	 */
    public function admin_html() {
    
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
		}

        ?>

		<style>

		.trimpress-admin .explainer {
			font-size: 14px;
		}

		.trimpress-admin .explainer span {
			display: inline-block;
			width: 3px;
			height: 14px;
			margin-bottom: -2px;
			border-radius: 1px;
		}

		.trimpress-admin .explainer span:nth-child(1) {
			background-color: #46b450;
		}

		.trimpress-admin .explainer span:nth-child(4) {
			background-color: #00A0D2;
		}

		.trimpress-admin hr {
			margin-top: 20px;
		}

		</style>

        <div class="wrap trimpress-admin">
        	
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<p class="explainer">
				
				<?php 
				
				$green_explainer = __( '<strong>Green</strong> indicates a setting is completely safe to implement.', 'trimpress' );
				$blue_explainer  = __( '<strong>Blue</strong> indicates you should proceed with more caution and know the implications.', 'trimpress' );
				
				echo sprintf( '<span></span> %s<br><span></span> %s', $green_explainer, $blue_explainer );
				
				?>
				
			</p>

			<button id="btn-all-none" class="button button-secondary" style="width: 80px; height: 32px;"><?php _e( 'Select', 'trimpress' ); ?></button>
			<select id="opt-all-none" style="width: 80px; height: 32px;">
				<option value="all"><?php _e( 'All', 'trimpress' ); ?></option>
				<option value="none"><?php _e( 'None', 'trimpress' ); ?></option>
			<select>
			
			<hr>
			
			<form action="options.php" method="post">
			
			<?php
			settings_fields( 'trimpress' );
			do_settings_sections( 'trimpress' );
			submit_button( __( 'Save Settings', 'trimpress' ) );
			?>

			</form>

		</div>

		<script>

		/**
	 	 * The select all/none functionality.
		 */
		let btn = document.getElementById('btn-all-none');
		let opt = document.getElementById('opt-all-none');
		let checkboxes = document.querySelectorAll('.trimpress-admin input[type="checkbox"]');
		btn.addEventListener('click', function() {
			if (opt.value === 'all') {
				for (let i = 0; i <= checkboxes.length; i += 1) {
					checkboxes[i].checked = true;
				}
			} else {
				for (let i = 0; i <= checkboxes.length; i += 1) {
					checkboxes[i].checked = false;
				}
			}
		});

		</script>

        <?php
	}
}
