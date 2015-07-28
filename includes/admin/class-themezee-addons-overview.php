<?php
/**
 * Registers the Addons Overview Class
 *
 */

 /* Use class to avoid namespace collisions */
if ( ! class_exists('ThemeZee_Addons_Overview') ) :

class ThemeZee_Addons_Overview {

	/* Setup the ThemeZee Addons Overview page */
	static function setup() {
		
		/* Add overview page to admin menu */
		add_action( 'admin_menu', array( __CLASS__, 'add_overview_page' ), 8 );

		/* Enqueue Admin Page Styles */
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_styles' ) );

	}
	
	/* Add Settings Page to Admin menu */
	static function add_overview_page() {
			
		add_menu_page(
			__( 'ThemeZee Add-ons', 'themezee-widget-bundle' ),
			__( 'Add-ons', 'themezee-widget-bundle' ),
			'manage_options',
			'themezee-add-ons',
			array( __CLASS__, 'display_overview_page' ),
			'dashicons-index-card'
		);
		add_submenu_page(
			'themezee-add-ons',
			__( 'ThemeZee Add-ons', 'themezee-widget-bundle' ),
			__( 'Overview', 'themezee-widget-bundle' ),
			'manage_options',
			'themezee-add-ons',
			array( __CLASS__, 'display_overview_page' )
		); 
		
	}
	
	/* Display Settings Page */
	static function display_overview_page() { ?>
		
		<div id="themezee-addons-wrap" class="wrap">
			
			<h2><?php _e( 'ThemeZee Add-ons', 'themezee-widget-bundle' ); ?></h2>

			<div id="themezee-addons-list" class="themezee-addons-clearfix">
			
				<?php do_action('themezee_addons_overview_page'); ?>
				
			</div>
			
		</div>
<?php	
	}

	/* Enqueue Admin Styles */
	static function enqueue_admin_styles( $hook ) {

		// Embed stylesheet only on admin settings page
		if( 'toplevel_page_themezee-add-ons' != $hook )
			return;
				
		// Enqueue Admin CSS
		wp_enqueue_style( 'themezee-addons-stylesheet', plugins_url('/css/themezee-addons.css', dirname( __FILE__ ) ), array(), '20140604' );
		
	}
	
}

/* Run Admin Class */
ThemeZee_Addons_Overview::setup();

endif;