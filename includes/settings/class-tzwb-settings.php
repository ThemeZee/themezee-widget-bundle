<?php
/**
 * Registers the TZWB Plugin settings Page
 *
 */

 /* Use class to avoid namespace collisions */
if ( ! class_exists('TZWB_Settings') ) :

class TZWB_Settings {

	/* Setup the TZWB settings page */
	static function setup() {
		
		// Register plugin settings
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );

		// Register License Functions
		add_action( 'admin_init', array( __CLASS__, 'activate_license' ) );
		add_action( 'admin_init', array( __CLASS__, 'deactivate_license' ) );
		add_action( 'admin_init', array( __CLASS__, 'check_license' ) );
		
	}
	
	/* Return TZWB options */
	static function get_options() {
		
		// Merge Plugin Options Array from Database with Default Options Array
		$plugin_options = wp_parse_args( 
			
			// Get saved theme options from WP database
			get_option( 'tzwb_settings' , array() ), 
			
			// Merge with Default Options if setting was not saved yet
			 self::default_options()
			
		);

		// Return theme options
		return $plugin_options;
		
	}
	
	/* Default TZWB options */
	static function default_options() {

		$default_options = array(
			'widget_visibility' => false,
			'active_widgets' => array(
				'tzwb_author_posts' => true,
				'tzwb_category_posts' => true,
				'tzwb_popular_posts' => true,
				'tzwb_recent_comments' => true,
				'tzwb_recent_posts' => true,
				'tzwb_social_icons' => true,
				'tzwb_tabbed_content' => true
				),
			'license_key' => '',
			'license_status' => 0
		);
		
		return $default_options;
	}
	
	
	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0
	 * @return array
	*/
	static function get_registered_settings() {
		
		$settings = array(
				'widget_visibility' => array(
						'title' =>  __('Visibility', 'themezee-widget-bundle'),
						'desc' => __('Add "Visibility" tab to widget settings to set conditions where the widget should be displayed.', 'themezee-widget-bundle'),
						'section' => 'general',
						'type' => 'checkbox'
					),
				'active_widgets' => array(
						'title' => __( 'Active Widgets', 'themezee-widget-bundle' ),
						'desc' => __( 'Choose available widgets.', 'themezee-widget-bundle' ),
						'section' => 'general',
						'type' => 'multicheck',
						'options' => array(	
								'tzwb_author_posts' => __('Enable Author Posts Widget', 'themezee-widget-bundle'),	
								'tzwb_category_posts' => __('Enable Category Posts Widget', 'themezee-widget-bundle'),	
								'tzwb_popular_posts' => __('Enable Popular Posts Widget', 'themezee-widget-bundle'),	
								'tzwb_recent_comments' => __('Enable Recent Comments Widget', 'themezee-widget-bundle'),	
								'tzwb_recent_posts' => __('Enable Recent Posts Widget', 'themezee-widget-bundle'),	
								'tzwb_social_icons' => __('Enable Social Icons Widget', 'themezee-widget-bundle'),	
								'tzwb_tabbed_content' => __('Enable Tabbed Content Widget', 'themezee-widget-bundle')	
							)
					),
				'license_key' => array(
						'title' =>  __('License Key', 'themezee-widget-bundle'),
						'desc' => __('Adds a "Visibility" tab to widget settings to set conditions where the widget should be displayed.', 'themezee-widget-bundle'),
						'section' => 'license',
						'type' => 'license'
					)
				);
			

		return apply_filters( 'tzwb_settings', $settings );
	}

	
	/**
	 * Add all settings sections and fields
	 *
	 * @since 1.0
	 * @return void
	*/
	static function register_settings() {

		if ( false == get_option( 'tzwb_settings' ) ) {
			add_option( 'tzwb_settings' );
		}
		
		// Add Sections
		add_settings_section( 'tzwb_settings_general', __('General', 'themezee-widget-bundle' ), '__return_false', 'tzwb_settings' );
		add_settings_section( 'tzwb_settings_license', __('License', 'themezee-widget-bundle'), '__return_false', 'tzwb_settings' );
		
		// Add Settings
		foreach ( self::get_registered_settings() as $key => $option ) {
		
			add_settings_field(
				'tzwb_settings[' . $key . ']',
				$option['title'],
				is_callable( array( __CLASS__, $option[ 'type' ] . '_callback' ) ) ? array( __CLASS__, $option[ 'type' ] . '_callback' ) : array( __CLASS__, 'missing_callback' ),
				'tzwb_settings',
				'tzwb_settings_' . $option['section'],
					array(
						'id'      => $key,
						'title'    => isset( $option['title'] ) ? $option['title'] : null,
						'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
						'section' => $option['section'],
						'size'    => isset( $option['size'] ) ? $option['size'] : null,
						'max'     => isset( $option['max'] ) ? $option['max'] : null,
						'min'     => isset( $option['min'] ) ? $option['min'] : null,
						'step'    => isset( $option['step'] ) ? $option['step'] : null,
						'options' => isset( $option['options'] ) ? $option['options'] : '',
						'std'     => isset( $option['std'] ) ? $option['std'] : ''
					)
			);
		}
		
		// Creates our settings in the options table
		register_setting( 'tzwb_settings', 'tzwb_settings', array( __CLASS__, 'sanitize_settings' ) );

	}
	
	
	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $options Array of all the AffiliateWP Options
	 * @return void
	 */
	static function checkbox_callback( $args ) {

		$options = self::get_options();
		
		$checked = isset($options[$args['id']]) ? checked(1, $options[$args['id']], false) : '';
		$html = '<input type="checkbox" id="tzwb_settings[' . $args['id'] . ']" name="tzwb_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
		$html .= '<label for="tzwb_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $options Array of all the AffiliateWP Options
	 * @return void
	 */
	static function multicheck_callback( $args ) {

		$options = self::get_options();
		
		if ( ! empty( $args['options'] ) ) {
			foreach( $args['options'] as $key => $option ) {
				if( isset( $options[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
				echo '<input name="tzwb_settings[' . $args['id'] . '][' . $key . ']" id="tzwb_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
				echo '<label for="tzwb_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
			}
			echo '<p class="description">' . $args['desc'] . '</p>';
		}
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $options Array of all the AffiliateWP Options
	 * @return void
	 */
	static function radio_callback( $args ) {

		$options = self::get_options();
		
		foreach ( $args['options'] as $key => $option ) :
			$checked = false;

			if ( isset( $options[ $args['id'] ] ) && $options[ $args['id'] ] == $key )
				$checked = true;
			elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $options[ $args['id'] ] ) )
				$checked = true;

			echo '<input name="tzwb_settings[' . $args['id'] . ']"" id="tzwb_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
			echo '<label for="tzwb_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
		endforeach;

		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $options Array of all the AffiliateWP Options
	 * @return void
	 */
	static function text_callback( $args ) {

		$options = self::get_options();
		
		if ( isset( $options[ $args['id'] ] ) )
			$value = $options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="tzwb_settings[' . $args['id'] . ']" name="tzwb_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<label for="tzwb_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * License Callback
	 *
	 * Renders license key fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $options Array of all the AffiliateWP Options
	 * @return void
	 */
	static function license_callback( $args ) {

		$options = self::get_options();
		
		if ( isset( $options[ $args['id'] ] ) )
			$value = $options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="tzwb_settings[' . $args['id'] . ']" name="tzwb_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$license_status = $options[ 'license_status' ];
		$license_key = ! empty( $value ) ? $value : false;

		if( 'valid' === $license_status && ! empty( $license_key ) ) {
			$html .= '<input type="submit" class="button" name="tzwb_deactivate_license" value="' . esc_attr__( 'Deactivate License', 'themezee-widget-bundle' ) . '"/>';
			$html .= '<span style="color:green;">&nbsp;' . __( 'Your license is valid!', 'themezee-widget-bundle' ) . '</span>';
		} elseif( 'expired' === $license_status && ! empty( $license_key ) ) {
			$renewal_url = add_query_arg( array( 'edd_license_key' => $license_key, 'download_id' => 41305 ), 'https://affiliatewp.com/checkout' );
			$html .= '<a href="' . esc_url( $renewal_url ) . '" class="button-primary">' . __( 'Renew Your License', 'themezee-widget-bundle' ) . '</a>';
			$html .= '<br/><span style="color:red;">&nbsp;' . __( 'Your license has expired, renew today to continue getting updates and support!', 'themezee-widget-bundle' ) . '</span>';
		} else {
			$html .= '<input type="submit" class="button" name="tzwb_activate_license" value="' . esc_attr__( 'Activate License', 'themezee-widget-bundle' ) . '"/>';
		}

		$html .= '<br/><label for="tzwb_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @since 1.9
	 * @param array $args Arguments passed by the setting
	 * @global $options Array of all the AffiliateWP Options
	 * @return void
	 */
	static function number_callback( $args ) {

		$options = self::get_options();
		
		if ( isset( $options[ $args['id'] ] ) )
			$value = $options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$max  = isset( $args['max'] ) ? $args['max'] : 999999;
		$min  = isset( $args['min'] ) ? $args['min'] : 0;
		$step = isset( $args['step'] ) ? $args['step'] : 1;

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="tzwb_settings[' . $args['id'] . ']" name="tzwb_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<label for="tzwb_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $options Array of all the AffiliateWP Options
	 * @return void
	 */
	static function textarea_callback( $args ) {

		$options = self::get_options();
		
		if ( isset( $options[ $args['id'] ] ) )
			$value = $options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<textarea class="large-text" cols="50" rows="5" id="tzwb_settings[' . $args['id'] . ']" name="tzwb_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
		$html .= '<label for="tzwb_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Missing Callback
	 *
	 * If a function is missing for settings callbacks alert the user.
	 *
	 * @since 1.3.1
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	static function missing_callback($args) {
		printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'themezee-widget-bundle' ), $args['id'] );
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $options Array of all the AffiliateWP Options
	 * @return void
	 */
	static function select_callback($args) {

		$options = self::get_options();
		
		if ( isset( $options[ $args['id'] ] ) )
			$value = $options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$html = '<select id="tzwb_settings[' . $args['id'] . ']" name="tzwb_settings[' . $args['id'] . ']"/>';

		foreach ( $args['options'] as $option => $name ) :
			$selected = selected( $option, $value, false );
			$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
		endforeach;

		$html .= '</select>';
		$html .= '<label for="tzwb_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Rich Editor Callback
	 *
	 * Renders rich editor fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $options Array of all the AffiliateWP Options
	 * @global $wp_version WordPress Version
	 */
	static function rich_editor_callback( $args ) {

		$options = self::get_options();
		
		if ( isset( $options[ $args['id'] ] ) )
			$value = $options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		ob_start();
		wp_editor( stripslashes( $value ), 'tzwb_settings[' . $args['id'] . ']', array( 'textarea_name' => 'tzwb_settings[' . $args['id'] . ']' ) );
		$html = ob_get_clean();

		$html .= '<br/><label for="tzwb_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	
	/* Validate and Save Settings */
	static function validate_settings( $settings ) {
		
		/* Validate General Settings */
		$settings['widget_visibility'] = !empty($settings['widget_visibility']);
		
		/* Validate Widget Settings */
		#foreach ( $widgets as $widget ) :
			#$settings['active_widgets'][$widget['id']] = !empty($settings['active_widgets'][$widget['id']]);
		#endforeach;
		
		return $settings;
	}
	
	/**
	 * Sanitize Plugin Settings
	 *
	 * @since 1.0
	 * @return array
	*/
	static function sanitize_settings( $input = array() ) {

		$options = self::get_options();
		
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		parse_str( $_POST['_wp_http_referer'], $referrer );

		$saved    = get_option( 'tzwb_settings', array() );
		if( ! is_array( $saved ) ) {
			$saved = array();
		}
		$settings = self::get_registered_settings();

		$input = $input ? $input : array();

		// Ensure a value is always passed for every checkbox
		if( ! empty( $settings) ) {
			foreach ( $settings as $key => $setting ) {

				// Single checkbox
				if ( isset( $settings[ $key ][ 'type' ] ) && 'checkbox' == $settings[ $key ][ 'type' ] ) {
					$input[ $key ] = ! empty( $input[ $key ] );
				}

				// Multicheck list
				if ( isset( $settings[ $key ][ 'type' ] ) && 'multicheck' == $settings[ $key ][ 'type' ] ) {
					if( empty( $input[ $key ] ) ) {
						$input[ $key ] = array();
					}
				}
			}
		}
		
		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ( $input as $key => $value ) {

			// Get the setting type (checkbox, select, etc)
			$type = isset( $settings[ $key ][ 'type' ] ) ? $settings[ $key ][ 'type' ] : false;

			if ( $type ) {
				// Field type specific filter
				$input[$key] = apply_filters( 'tzwb_settings_sanitize_' . $type, $value, $key );
			}

			// General filter
			$input[ $key ] = apply_filters( 'tzwb_settings_sanitize', $value, $key );
		}
		
		return array_merge( $saved, $input );

	}
	
	static function activate_license() {
		
		if( ! isset( $_POST['tzwb_settings'] ) )
			return;

		if( ! isset( $_POST['tzwb_activate_license'] ) )
			return;

		if( ! isset( $_POST['tzwb_settings']['license_key'] ) )
			return;

		// Retrieve license from the database
		$options = self::get_options();
		$status  = $options['license_status'];
		$license = trim( $_POST['tzwb_settings']['license_key'] );

		if( 'valid' == $status )
			return; // license already activated and valid

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( TZWB_NAME ),
			'item_id'   => 41305,
			'url'       => home_url()
		);
		
		// Call the custom API.
		$response = wp_remote_post( TZWB_STORE_API_URL, array( 'timeout' => 25, 'sslverify' => true, 'body' => $api_params ) );
		
		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		// $license_data->license will be either "valid" or "invalid"
		$options['license_status'] = $license_data->license;
		$options['license_status'] = 'valid';
		
		// Update Plugin Options
		update_option( 'tzwb_settings', $options );

		delete_transient( 'tzwb_license_check' );
	}
	
	
	static function deactivate_license() {

		if( ! isset( $_POST['tzwb_settings'] ) )
			return;

		if( ! isset( $_POST['tzwb_deactivate_license'] ) )
			return;

		if( ! isset( $_POST['tzwb_settings']['license_key'] ) )
			return;

		// retrieve the license from the database
		$license = trim( $_POST['tzwb_settings']['license_key'] );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( TZWB_NAME ),
			'url'       => home_url()
		);
		
		// Call the custom API.
		$response = wp_remote_post( TZWB_STORE_API_URL, array( 'timeout' => 25, 'sslverify' => true, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		$options = self::get_options();

		$options['license_status'] = 0;

		update_option( 'tzwb_settings', $options );

		delete_transient( 'tzwb_license_check' );

	}

	
	static function check_license() {

		if( ! empty( $_POST['tzwb_settings'] ) ) {
			return; // Don't fire when saving settings
		}

		$status = get_transient( 'tzwb_license_check' );

		// Run the license check a maximum of once per day
		if( false === $status ) {
		
			// retrieve the license from the database
			$license = trim( $_POST['tzwb_settings']['license_key'] );
			
			// data to send in our API request
			$api_params = array(
				'edd_action'=> 'check_license',
				'license' 	=> $license,
				'item_name' => urlencode( TZWB_NAME ),
				'url'       => home_url()
			);
			
			// Call the custom API.
			$response = wp_remote_post( TZWB_STORE_API_URL, array( 'timeout' => 25, 'sslverify' => true, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			$options = self::get_options();

			$options['license_status'] = $license_data->license;

			update_option( 'tzwb_settings', $options );

			set_transient( 'tzwb_license_check', $license_data->license, DAY_IN_SECONDS );

			$status = $license_data->license;

		}

		return $status;

	}

	public function is_license_valid() {
		return $this->check_license() == 'valid';
	}
	
}

/* Run Admin Class */
TZWB_Settings::setup();

endif;