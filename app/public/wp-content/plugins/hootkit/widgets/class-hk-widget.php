<?php
/**
 * Functions for registering and setting widgets. This file loads an abstract class to help
 * build widgets, and loads individual widget classes for building widgets into the backend and
 * loading their template for displaying in frontend
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Abstract Widgets Class for creating and displaying widgets.
 * @todo customize_selective_refresh and cached widgets
 * @todo try groups with <script type="text/html"> templating
 * 
 * @credit  Inspired from Vantage theme code by Greg Priday http://SiteOrigin.com
 *          Licensed under GPL
 * 
 * @since 1.0.0
 * @access public
 */
if ( !class_exists( 'HK_Widget' ) ):
abstract class HK_Widget extends WP_Widget {

	protected $form_options;
	protected $repeater_html;
	protected $widgetid;

	/**
	 * Register the widget and load the Widget options
	 * 
	 * @since 1.0.0
	 */
	function __construct( $id, $name, $widget_options = array(), $control_options = array(), $form_options = array() ) {
		$this->form_options = $form_options;
		$this->widgetid = $id;
		$name = hootkit()->get_string('widget-prefix') . $name;
		parent::__construct( $id, $name, $widget_options, $control_options );

		$this->initialize();
	}

	/**
	 * Initialize this widget in whatever way we need to. Runs before rendering widget or form.
	 *
	 * @since 1.0.0
	 */
	function initialize(){ }

	/**
	 * Display the widget.
	 *
	 * @since 1.0.0
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		$args = wp_parse_args( $args, array(
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => '',
		) );

		$defaults = array();
		foreach( $this->form_options as $id => $field ) {
			if ( isset( $field['std'] ) ) {
				$defaults[ $id ] = $field['std'];
			}
			// SiteOrigin Page Builder compatibility
			// > new widget instance doesnt have all option values when post is saved without editing widget even once (in Gutenberg only)
			// > Hence build value array if this is a widget in SO layout (in case widget was added but never edited even once)
			elseif( isset( $instance['panels_info'] ) && isset( $field['type'] ) ) {
				switch ( $field['type'] ) :
					case 'text' :       $defaults[ $id ] = '';      break;
					case 'textarea' :   $defaults[ $id ] = '';      break;
					case 'separator' :                              break;
					case 'checkbox':    $defaults[ $id ] = 0;       break;
					case 'select':      if ( !empty( $field['options'] ) && is_array( $field['options'] ) ) 
											foreach ( $field['options'] as $sopk => $sopv ) { $defaults[ $id ] = $sopk; break; }
										else $defaults[ $id ] = '';
										break;
					case 'multiselect': $defaults[ $id ] = array(); break;
					case 'smallselect': if ( !empty( $field['options'] ) && is_array( $field['options'] ) ) 
											foreach ( $field['options'] as $sopk => $sopv ) { $defaults[ $id ] = $sopk; break; }
										else $defaults[ $id ] = '';
										break;
					case 'radio':
					case 'images':      if ( !empty( $field['options'] ) && is_array( $field['options'] ) ) 
											foreach ( $field['options'] as $sopk => $sopv ) { $defaults[ $id ] = $sopk; break; }
										else $defaults[ $id ] = '';
										break;
					case 'icon':        $defaults[ $id ] = '';      break;
					case 'image':       $defaults[ $id ] = '';      break;
					case 'color':       $defaults[ $id ] = '';      break;
					case 'group':       $defaults[ $id ] = array(); break;
					case 'collapse':    $defaults[ $id ] = array(); break;
				endswitch;
			}

			if ( !empty( $field['optionsfn'] ) ) {
				$optionsfn = $field['optionsfn'];
				unset( $field['optionsfn'] );
				if ( function_exists( $optionsfn ) ) $field['options'] = $optionsfn();
			}

		}

		$instance = wp_parse_args( $instance, $defaults );
		$instance = apply_filters( 'hootkit_display_widgetinstance', $instance, $this->widgetid, $args );

		global $hoot_data;
		$hoot_data->currentwidget = array(
			'id'       => $this->widgetid,
			'widget'   => $args,
			'instance' => $instance,
		);

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		// SiteOrigin Page Builder compatibility - Live Preview in backend
		// > undefined functions like hootkit_thumbnail_size, hootkit_widget_borderclass etc.
		// > These files are included only in !is_admin() by default, so add them now. // @todo test all widgets (inc sliders)
		// require_once( hootkit()->dir . 'include/template.php' );
		if ( is_admin() ) { esc_html_e( $title ); printf( esc_html__( '%1$sThis widget preview is not available in the Edit screen.%2$s', 'hootkit' ), '<div style="background: #eee; border: inset 1px #ddd; padding: 3px 10px; border-radius: 3px; opacity: 0.8; font-style: italic; font-size: 0.95em;">', '</div>' ); return; }

		echo apply_filters( 'hootkit_before_widget', $args['before_widget'], $instance );
			$this->display_widget( $instance, $args['before_title'], $title, $args['after_title'] );
		echo apply_filters( 'hootkit_after_widget', $args['after_widget'], $instance );
	}

	/**
	 * Echo the widget content
	 * Subclasses should over-ride this function to generate their widget code.
	 * Convention: Subclasses should include the template from the theme/widgets folder.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	function display_widget( $instance, $before_title = '', $title = '', $after_title = '' ) {
		die('function Hoot_WP_Widget::display_widget() must be over-ridden in a sub-class.');
	}

	/**
	 * Update the widget instance.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array|void
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance = $this->sanitize( $new_instance, $this->form_options );
		return $new_instance;
	}

	/**
	 * Display the widget form.
	 *
	 * @since 1.0.0
	 * @param array $instance
	 * @return string|void
	 */
	public function form( $instance ) {
		$form_id = 'hoot-widget-form-' . md5( uniqid( rand(), true ) );
		$class_name = str_replace( '_', '-', strtolower( get_class($this) ) ); ?>

		<div class="hoot-widget-form hoot-widget-form-<?php echo esc_attr( $class_name ) ?>" id="<?php echo $form_id ?>" data-class="<?php echo get_class($this) ?>">

			<?php if ( !empty( $this->widget_options['help'] ) ) : ?>
				<div class="hoot-widget-form-help"><?php echo $this->widget_options['help']; ?></div>
			<?php endif;

			foreach( $this->form_options as $id => $field ) {
				$field = wp_parse_args( (array) $field, array( 	'name'     => '',
																'desc'     => '',
																'type'     => '',
																'settings' => array(),
																'std'      => '',
																'options'  => array(),
																'fields'   => array(),
														) );
				if ( !is_string( $id ) || empty( $field['type'] ) ) continue;

				$value = false;
				if ( isset( $instance[ $id ] ) ) $value = $instance[ $id ];
				elseif ( !empty( $field['std'] ) ) $value = $field['std'];

				if ( !empty( $field['optionsfn'] ) ) {
					$optionsfn = $field['optionsfn'];
					unset( $field['optionsfn'] );
					if ( function_exists( $optionsfn ) ) $field['options'] = $optionsfn();
				}

				$this->render_field( $id, $field, $value );
			} ?>
			<script type="text/javascript">
				( function($){
					if (typeof window.hoot_widget_helper == 'undefined')
						window.hoot_widget_helper = {};
					<?php /*if (typeof window.hoot_widget_helper["<?php echo get_class($this) ?>"] == 'undefined')*/ // This creates unexpected results as the script is first instancized in template widget __i__ ?>
						window.hoot_widget_helper["<?php echo get_class($this) ?>"] = <?php echo json_encode( $this->repeater_html ) ?>;
					if (typeof $.fn.hootSetupWidget != 'undefined') { // console.log('inline calls setup');
						$('#<?php echo $form_id ?>').hootSetupWidget(); // Needed for Customizer
					}
				} )( jQuery );
			</script>
		</div><?php
	}

	/**
	 * Render a form field
	 *
	 * @since 1.0.0
	 * @param $id
	 * @param $field
	 * @param $value
	 * @param $repeater
	 */
	function render_field( $id, $field, $value, $repeater = array() ){
		extract( $field, EXTR_SKIP );

		?><div class="hoot-widget-field hoot-widget-field-type-<?php echo ( strlen( $type ) < 15 ) ? sanitize_html_class( $type ) : 'custom' ?> hoot-widget-field-<?php echo sanitize_html_class( $id ) ?>"><?php

			if ( !empty( $name ) && $type != 'checkbox' && $type != 'separator' && $type != 'group' && $type != 'collapse' ) { ?>
				<label for="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>"><?php echo $name ?>:</label>
			<?php }

			switch( $type ) {
				case 'text' :
					$size = ( isset( $settings['size'] ) && is_numeric( $settings['size'] ) ) ? ' size="' . $settings['size'] . '" ' : '';
					$class = ( isset( $settings['size'] ) && is_numeric( $settings['size'] ) ) ? '' : ' widefat ';
					$placeholder = ( isset( $settings['placeholder'] ) ) ? ' placeholder="' . esc_attr( $settings['placeholder'] ) . '" ' : '';
					?><input type="text" name="<?php echo $this->hoot_get_field_name( $id, $repeater ) ?>" id="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>" value="<?php echo esc_attr( $value ) ?>" class="hoot-widget-input<?php echo $class; ?>" <?php echo $size . $placeholder; ?> /><?php
					break;

				case 'textarea' :
					$rows = ( isset( $settings['rows'] ) && is_numeric( $settings['rows'] ) ) ? intval( $settings['rows'] ) : 4;
					$placeholder = ( isset( $settings['placeholder'] ) ) ? ' placeholder="' . esc_attr( $settings['placeholder'] ) . '" ' : '';
					?><textarea name="<?php echo $this->hoot_get_field_name( $id, $repeater ) ?>" id="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>" class="widefat hoot-widget-input" rows="<?php echo $rows; ?>" <?php echo $placeholder ?>><?php echo esc_textarea( $value ) ?></textarea><?php
					break;

				case 'separator' :
					if ( empty( $settings['hideborder'] ) ): ?><div class="hoot-widget-field-separator"></div><?php endif;
					$style = ( !empty( $desc ) ) ? ' style="margin-bottom:0;"' : '';
					if ( !empty( $name ) ) echo "<h4{$style}>{$name}</h4>";
					break;

				case 'checkbox':
					?><label for="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>">
						<input type="checkbox" name="<?php echo $this->hoot_get_field_name( $id, $repeater ) ?>" id="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>" class="hoot-widget-input" <?php checked( !empty( $value ) ) ?> />
						<?php echo $name ?>
					</label><?php
					break;

				case 'select':
					?><select name="<?php echo $this->hoot_get_field_name( $id, $repeater ) ?>" id="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>" class="hoot-widget-input widefat">
						<?php foreach( $options as $k => $v ) : ?>
							<option value="<?php echo esc_attr($k) ?>" <?php selected($k, $value) ?>><?php echo esc_html($v) ?></option>
						<?php endforeach; ?>
					</select><?php
					break;

				case 'multiselect':
					if ( is_string( $value ) ) { $value = array( $value ); } // Pre 1.0.10 compatibility with 'select' type
					$value = is_array( $value ) ? $value : array(); // false when none selected
					?><select name="<?php echo $this->hoot_get_field_name( $id, $repeater ) ?>[]" id="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>" class="hoot-widget-input widefat hoot-select2" multiple="multiple">
						<?php foreach( $options as $k => $v ) : ?>
							<option value="<?php echo esc_attr($k) ?>" <?php if ( in_array( $k, $value ) ) echo 'selected="selected"'; ?>><?php echo esc_html($v) ?></option>
						<?php endforeach; ?>
					</select><?php
					break;

				case 'smallselect':
					?><select name="<?php echo $this->hoot_get_field_name( $id, $repeater ) ?>" id="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>" class="hoot-widget-input hootsmallselect">
						<?php foreach( $options as $k => $v ) : ?>
							<option value="<?php echo esc_attr($k) ?>" <?php selected($k, $value) ?>><?php echo esc_html($v) ?></option>
						<?php endforeach; ?>
					</select><?php
					break;

				case 'radio': case 'images':
					?><ul id="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>-list" class="hoot-widget-list hoot-widget-list-<?php echo $type ?>">
						<?php foreach( $options as $k => $v ) : ?>
							<li class="hoot-widget-list-item">
								<input type="radio" class="hoot-widget-input" name="<?php echo $this->hoot_get_field_name( $id, $repeater ) ?>" id="<?php echo $this->hoot_get_field_id( $id, $repeater ) . '-' . sanitize_html_class( $k ) ?>" value="<?php echo esc_attr($k) ?>" <?php checked( $k, $value ) ?>>
								<label for="<?php echo $this->hoot_get_field_id( $id, $repeater ) . '-' . sanitize_html_class( $k ) ?>"><?php echo ( 'radio' === $type ) ? $v : "<img class='hoot-widget-image-picker-img' src='" . esc_url( $v ) . "'>" ?></label>
							</li>
						<?php endforeach; ?>
					</ul><?php
					break;

				case 'icon':
					$iconvalue = hoot_sanitize_fa( $value );
					?><input id="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>" class="hoot-icon" name="<?php echo $this->hoot_get_field_name( $id, $repeater ) ?>" type="hidden" value="<?php echo esc_attr( $iconvalue ) ?>" />
					<div id="<?php echo $this->hoot_get_field_id( $id, $repeater ) . '-icon-picked' ?>" class="hoot-icon-picked"><i class="<?php echo esc_attr( $iconvalue ) ?>"></i><span><?php _e( 'Select Icon', 'hootkit' ) ?></span></div>
					<div class="clear"></div>
					<div id="<?php echo $this->hoot_get_field_id( $id, $repeater ) . '-icon-picker-box' ?>" class="hoot-icon-picker-box">
						<div class="hoot-icon-picker-list"><i class="fas fa-ban hoot-icon-none" data-value="0" data-category=""><span><?php _e( 'Remove Icon', 'hootkit' ) ?></span></i></div>
						<?php
						$section_icons = ( !empty( $options ) && is_array( $options ) )? $options : hoot_enum_icons('icons');
						$sections = hoot_enum_icons('sections');
						foreach ( $section_icons as $s_key => $s_array ) { ?>
							<?php if ( !empty( $sections[ $s_key ] ) ) echo '<h4>'.$sections[ $s_key ].'</h4>'; elseif( is_string( $s_key ) ) echo '<h4>'.ucfirst( $s_key.'</h4>' ); else echo '<p></p>'; ?>
							<div class="hoot-icon-picker-list"><?php
							if ( is_array( $section_icons[$s_key] ) ) {
								foreach ( $section_icons[$s_key] as $i_key => $i_class ) {
									$selected = ( $iconvalue == $i_class ) ? ' selected' : '';
									?><i class='<?php echo $i_class . $selected; ?>' data-value='<?php echo $i_class; ?>' data-category='<?php echo $s_key ?>'></i><?php
								}
							} ?>
							</div><?php
						}
						?>
					</div><?php
					break;

				case 'image':
					?><input id="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>" class="hoot-image" name="<?php echo $this->hoot_get_field_name( $id, $repeater ) ?>" type="hidden" value="<?php echo esc_attr( $value ) ?>" />
					<div id="<?php echo $this->hoot_get_field_id( $id, $repeater ) . '-image-selected' ?>" class="hoot-image-selected" data-title="<?php _e( 'Select Image', 'hootkit' ) ?>" data-update="<?php _e( 'Set Image', 'hootkit' ) ?>" data-library="image"><span class="hoot-image-selected-img" <?php
						if ( !empty( $value ) ) {
							$post = get_post( $value );
							$src = wp_get_attachment_image_src( $value, 'thumbnail' );
							if( empty( $src ) ) $src = wp_get_attachment_image_src( $value, 'thumbnail', true );
							if ( !empty( $src[0] ) ) echo 'style="background-image: url(' . esc_attr( $src[0] ) . ')"';
						}
						?>></span><span class="hoot-image-selected-label"><?php _e( 'Add Image', 'hootkit' ) ?></span></div>
						<a href="#" class="hoot-image-remove"><?php _e( 'Remove Image', 'hootkit' ) ?></a>
					<?php
					break;

				case 'color':
					$default_color = ( !empty( $std ) ) ? 'data-default-color="' . $std . '"' : '';
					?><input id="<?php echo $this->hoot_get_field_id( $id, $repeater ) ?>" class="hoot-color" name="<?php echo $this->hoot_get_field_name( $id, $repeater ) ?>" type="input" value="<?php echo esc_attr( $value ) ?>" <?php echo $default_color; ?> />
					<?php
					break;

				case 'group':
					$repeater[] = $id;
					?><div class="hoot-widget-field-group" data-id="<?php echo esc_attr( $id ) ?>">
						<?php if ( !empty( $name ) ): ?>
							<div class="hoot-widget-field-group-top">
								<h3><?php echo $name ?> <i class="fas fa-sort"></i></h3>
							</div>
						<?php endif; ?>
						<?php $item_name = isset( $options['item_name'] ) ? $options['item_name'] : ''; ?>
						<div class="hoot-widget-field-group-items<?php if ( !empty( $options['sortable'] ) ) echo ' issortable'; ?>">
							<?php
							$max = ( isset( $options['maxlimit'] ) ) ? absint( $options['maxlimit'] ) : 0;
							$max = ( $max < 1 || 4 < $max ) ? 4 : $max;
							$groupcount = 0;
							if ( !empty( $value ) ) {
								foreach( $value as $k =>$v ) {
									$groupcount++;
									$this->render_group( $k, $v, $fields, $item_name, $repeater );
									if ( empty( $options['dellimit'] ) && $groupcount >= $max ) break;
								}
							} ?>
						</div>
						<?php
							ob_start();
							$this->render_group( 975318642, array(), $fields, $item_name, $repeater );
							$html = ob_get_clean();
							$this->repeater_html[$id] = $html;
							$limitval = ( empty( $options['dellimit'] ) ) ? ' data-limit="' . $max . '"' : '';
							$limitmsg = ( isset( $options['limitmsg'] ) ) ? ' data-limitmsg="' . esc_attr( $options['limitmsg'] ) . '"' : '';
						?>
						<div id="add-<?php echo rand(1000, 9999); ?>" class="hoot-widget-field-group-add<?php if ( empty( $options['dellimit'] ) && $groupcount >= $max ) echo ' maxreached'; ?>" data-iterator="<?php echo is_array( $value ) ? max( array_keys( $value ) ) : 0; ?>" <?php echo $limitval.$limitmsg ?>><?php _e('Add', 'hootkit') ?></div>
					</div>
					<?php
					break;

				case 'collapse':
					$repeater[] = $id;
					$bodystyle = ( isset( $settings['state'] ) && ( $settings['state'] == 'open' ) ) ? ' style="display:block;"' : '';
					?><div class="hoot-widget-field-collapse" data-id="<?php echo esc_attr( $id ) ?>">
						<div class="hoot-collapse-head"><h3><?php if ( !empty( $name ) ) echo esc_html($name); else _e('Group', 'hootkit') ?> <i class="fas fa-sort"></i></h3></div>
						<div class="hoot-collapse-body"<?php echo $bodystyle; ?>>
							<?php foreach( $fields as $subid => $subfield ) {
								$subfield = wp_parse_args( (array) $subfield, array( 	'name'     => '',
																						'desc'     => '',
																						'type'     => '',
																						'settings' => array(),
																						'std'      => '',
																						'options'  => array(),
																						'fields'   => array(),
																				) );
								if ( !is_string( $subid ) || empty( $subfield['type'] ) ) continue;

								$subvalue = false;
								if ( isset( $value[ $subid ] ) ) $subvalue = $value[ $subid ];
								elseif ( !empty( $subfield['std'] ) ) $subvalue = $subfield['std'];
								$this->render_field( $subid, $subfield, $subvalue, $repeater );
							} ?>
						</div>
					</div>
					<?php
					break;

				default:
					echo str_replace( array( '%id%', '%class%', '%name%', '%value%' ),
									  array( $this->hoot_get_field_id( $id, $repeater ), 'hoot-widget-input', $this->hoot_get_field_name( $id, $repeater ), $value ),
									  $type );
					break;

			}

			if ( ! empty( $desc ) )
				echo '<div class="hoot-widget-field-description"><small>' . wp_kses_post( $desc ) . '</small></div>';
			echo '<div class="clear"></div>';

		?></div><?php
	}

	/**
	 * Render a group field
	 *
	 * @since 1.0.0
	 * @param $field
	 * @param $value
	 * @param $repeater
	 */
	function render_group( $key, $value, $fields, $item_name = '', $repeater = array() ){
		if ( empty( $fields ) ) return;

		$repeater[] = intval( $key ); ?>
		<div class="hoot-widget-field-group-item">
			<div class="hoot-widget-field-group-item-top">
				<div class="hoot-widget-field-group-remove">X</div>
				<h4><i class="fas fa-arrows-alt"></i><i class="fas fa-caret-down"></i> <?php echo esc_html( $item_name ) ?></h4>
			</div>
			<div class="hoot-widget-field-group-item-form">
				<?php foreach( $fields as $id => $field ) {
					$field = wp_parse_args( (array) $field, array( 	'name'     => '',
																	'desc'     => '',
																	'type'     => '',
																	'settings' => array(),
																	'std'      => '',
																	'options'  => array(),
																	'fields'   => array(),
															) );
					if ( !is_string( $id ) || empty( $field['type'] ) ) continue;

					$fieldvalue = false;
					if ( isset( $value[ $id ] ) ) $fieldvalue = $value[ $id ];
					elseif ( !empty( $field['std'] ) ) $fieldvalue = $field['std'];
					$this->render_field( $id, $field, $fieldvalue, $repeater );
				} ?>
			</div>
		</div><?php
	}

	/**
	 * @since 1.0.0
	 * @param $id
	 * @param array $repeater
	 * @return mixed|string
	 */
	public function hoot_get_field_name( $id, $repeater = array() ) {
		if ( empty( $repeater ) ) return $this->get_field_name( $id );
		else {
			$repeater_extras = '';
			foreach( $repeater as $r )
				$repeater_extras .= '[' . $r . ']';
			$repeater_extras .= '[' . esc_attr( $id ) . ']';
			$name = $this->get_field_name('{{{FIELD_NAME}}}');
			$name = str_replace( '[{{{FIELD_NAME}}}]', $repeater_extras, $name );
			return $name;
		}
	}

	/**
	 * Get the ID of this field.
	 *
	 * @since 1.0.0
	 * @param $id
	 * @param array $repeater
	 * @return string
	 */
	public function hoot_get_field_id( $id, $repeater = array() ) {
		if ( empty( $repeater ) ) return $this->get_field_id( $id );
		else {
			$ids = $repeater;
			$ids[] = $id;
			return $this->get_field_id( implode( '-', $ids ) );
		}
	}

	/**
	 * Sanitize field values to store in database
	 *
	 * @since 1.0.0
	 * @param $instance
	 * @param $fields
	 */
	public function sanitize( $instance, $fields ) {
		foreach ( $fields as $id => $field ) {

			if ( !empty( $field['optionsfn'] ) ) {
				$optionsfn = $field['optionsfn'];
				unset( $field['optionsfn'] );
				if ( function_exists( $optionsfn ) ) $field['options'] = $optionsfn();
			}

			/* Skip if the field does not have an id/type */
			if ( !is_string( $id ) || !isset( $field['type'] ) )
				continue;

			/* Skip if instance value is not set (except for checkbox) */
			if ( !isset( $instance[ $id ] ) && $field['type'] != 'checkbox' )
				continue;

			/* Clean value */
			$output = '';

			if ( !isset( $field['sanitize'] ) ) {

				/* Default field sanitization */
				switch ( $field['type'] ) {
					case 'text':
					case 'textarea':
						$output = wp_kses_post( $instance[ $id ] );
						break;
					case 'checkbox':
						$output = ( !empty( $instance[ $id ] ) ) ? 1 : 0;
						break;
					case 'select':
					case 'smallselect':
					case 'radio':
					case 'images':
						$output = ( isset( $field['options'][ $instance[ $id ] ] ) ) ? $instance[ $id ] : '';
						break;
					case 'multiselect':
						$output = array();
						// SiteOrigin Page Builder compatibility - check if value is $instance[ $id ] or stored in [0] of $instance[ $id ]
						// (value array stored in 1st term instead of root since select name is xxx[] : not so in widgets or customizer)
						$value = ( is_array( $instance[ $id ] ) && is_array( $instance[ $id ][0] ) ) ? $instance[ $id ][0] : $instance[ $id ];
						foreach ( $value as $check ) {
							if ( array_key_exists( $check, $field['options'] ) ) $output[] = $check;
						}
						break;
					case 'image':
						$output = absint( $instance[ $id ] ); // Image is stored as ID
						break;
					case 'color':
						$output = sanitize_hex_color( $instance[ $id ] );
						break;
					case 'icon':
						$icons = hoot_enum_icons();
						$output = ( in_array( $instance[ $id ], $icons ) ) ? $instance[ $id ] : '';
						break;
					case 'group':
						$output = array();
						foreach ( $instance[ $id ] as $i => $subinstance ) {
							$output[ $i ] = $this->sanitize( $subinstance, $field['fields'] );
						}
						break;
					case 'collapse':
						$output = array();
						$output = $this->sanitize( $instance[$id], $field['fields'] );
						break;
					// Allow custom sanitization functions
					default:
						$output = apply_filters( 'hoot_admin_widget_sanitize_field', $instance[ $id ], $field['type'], $instance );
				}

			} elseif ( isset( $field['sanitize'] ) ) {

				/* Custom sanitizations for specific field. Example, a text input has a url */
				switch( $field['sanitize'] ) {
					case 'url':
						$output = esc_url_raw( $instance[ $id ] );
						break;
					case 'integer':
						if ( $instance[ $id ] === '0' || $instance[ $id ] === 0 ) {
							$output = 0;
						} else {
							$output = intval( $instance[ $id ] );
							$output = ( !empty( $output ) ) ? $output : '';
						}
						break;
					case 'absint':
						if ( $instance[ $id ] === '0' || $instance[ $id ] === 0 ) {
							$output = 0;
						} else {
							$output = absint( $instance[ $id ] );
							$output = ( !empty( $output ) ) ? $output : '';
						}
						break;
					case 'percent':
						if ( $instance[ $id ] === '0' || $instance[ $id ] === 0 ) {
							$output = 0;
						} elseif ( empty( $instance[ $id ] ) ) {
							$output = '';
						} else {
							$output = absint( $instance[ $id ] );
							$output = ( $output > 100 ) ? 100 : $output;
						}
						break;
					case 'email':
						$output = ( is_email( $instance[ $id ] ) ) ? sanitize_email( $instance[ $id ] ) : '';
						break;
					// Allow custom sanitization functions
					default:
						$output = apply_filters( 'hoot_admin_widget_sanitize_field', $instance[ $id ], $field['sanitize'], $instance );
				}

			}

			// Set clean value
			$instance[ $id ] = ( !empty( $output ) || 0 === $output || '0' === $output ) ? $output : '';
		}
		return $instance;
	}

}
endif;

/**
 * Add Widget ID information at the bottom of each widget
 * @credit https://spicewp.com/get-widget-id-wordpress/
 *
 * @since 1.0.11
 * @param object $widget_instance
 * @return void
 */
add_action( 'in_widget_form', 'hootkit_get_widget_id' );
function hootkit_get_widget_id( $widget_instance ) {
	// Check if the widget is already saved or not. 
	if ($widget_instance->number=="__i__"){
		echo '<p class="widgetid-display"><code>' . __( 'Widget ID:', 'hootkit' ) . ' <span class="widgetid">' . __( 'Please save the widget first!', 'hootkit' ) . '</span></code></p>';
	} else {
		echo '<p class="widgetid-display"><code>' . __( 'Widget ID:', 'hootkit' ) . ' <strong class="widgetid">' . $widget_instance->id . '</strong></code></p>';
	}
}