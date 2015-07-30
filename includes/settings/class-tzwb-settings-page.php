<?php
/***
 * TZWB Settings Page Class
 *
 * Adds the menu link in the backend and displays the settings page.
 *
 * @package ThemeZee Widget Bundle
 */
 

 /* Use class to avoid namespace collisions */
if ( ! class_exists('TZWB_Settings_Page') ) :

class TZWB_Settings_Page {

	/**
	 * Setup the Settings Page class
	 *
	 * @return void
	*/
	static function setup() {
		
		// Add settings page to admin menu
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ), 9 );
		
	}

	/**
	 * Add settings page link to admin menu
	 *
	 * @return void
	*/
	static function add_settings_page() {
			
		add_submenu_page(
			'themezee-add-ons',
			__( 'ThemeZee Widget Bundle Settings', 'themezee-widget-bundle' ),
			__( 'Widget Bundle', 'themezee-widget-bundle' ),
			'manage_options',
			'themezee-widget-bundle',
			array( __CLASS__, 'display_settings_page' )
		); 
		
	}
	
	/**
	 * Display settings page
	 *
	 * @return void
	*/
	static function display_settings_page() { 
	
		ob_start();

	?>
		
		<div id="tzwb-settings-wrap" class="wrap">
			
			<h2><?php _e( 'ThemeZee Widget Bundle Settings', 'themezee-widget-bundle' ); ?></h2>
			<?php settings_errors(); ?>
			
			<div class="tzwb-settings-container">

				<form method="post" action="options.php">
					<?php
						settings_fields('tzwb_settings');
						do_settings_sections('tzwb_settings');
						submit_button();
					?>
				</form>
				
			</div>
			
		</div>
<?php
		echo ob_get_clean();
	}
	
}

// Run Settings Page Class
TZWB_Settings_Page::setup();

endif;