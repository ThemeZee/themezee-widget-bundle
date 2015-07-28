<?php
/**
 * Registers the TZWB Plugin settings Page
 *
 */

 /* Use class to avoid namespace collisions */
if ( ! class_exists('TZWB_Settings_Page') ) :

class TZWB_Settings_Page {

	/* Setup the TZWB settings page */
	static function setup() {
		
		// Add settings page to admin menu
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ), 9 );

		// Enqueue Admin Page Styles
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_styles' ) );

	}

	
	/* Add Settings Page to Admin menu */
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
	
	/* Display Settings Page */
	static function display_settings_page() { 
	
		$options = TZWB_Settings::get_options();
		
		

	?>
		
		<div id="tzwb-admin-wrap" class="wrap">
			
			<h2><?php _e( 'ThemeZee Widget Bundle Settings', 'themezee-widget-bundle' ); ?></h2>
			<?php settings_errors(); ?>

			<div id="tzwb-admin-container" class="tzwb-admin-clearfix">
			
				<div id="tzwb-admin-content">

					<form method="post" action="options.php">
						<?php
							settings_fields('tzwb_settings');
							do_settings_sections('tzwb_settings');
							submit_button();

							print_r($options[ 'license_status' ]);
							echo 'test';
						?>
					</form>
					
				</div>
				
				<div id="tzwb-admin-sidebar" class="tzwb-admin-clearfix">
					<?php self::display_sidebar(); ?>
				</div>
				
			</div>
			
		</div>
<?php	
	}
	
	/* Display TZWB Admin Sidebar */
	static function display_sidebar() { ?>

		<dl><dt><h4><?php _e('Quick Links', 'themezee-widget-bundle'); ?></h4></dt>
			<dd>
				<ul>
					<li><a href="http://themezee.com/themes/zeedynamic/#PROVersion-1" target="_blank"><?php _e('Learn more about the PRO Version', 'themezee-widget-bundle'); ?></a></li>
					<li><a href="http://themezee.com/docs/" target="_blank"><?php _e('Theme Documentation', 'themezee-widget-bundle'); ?></a></li>
					<li><a href="http://wordpress.org/support/view/theme-reviews/zeedynamic" target="_blank"><?php _e('Rate zeeDynamic on wordpress.org', 'themezee-widget-bundle'); ?></a></li>
				</ul>
			</dd>
		</dl>
		
		<dl><dt><h4><?php _e('Help to translate', 'themezee-widget-bundle'); ?> </h4></dt>
			<dd>
				<p><?php _e('You want to use this WordPress theme in your native language? Then help out to translate it!', 'themezee-widget-bundle'); ?></p>
				<p><a href="http://translate.themezee.org/projects/zeedynamic" target="_blank"><?php _e('Join the Online Translation Project', 'themezee-widget-bundle'); ?></a></p>
			</dd>
		</dl>

	<?php
	}
	

	/* Enqueue Admin Styles */
	static function enqueue_admin_styles( $hook ) {

		// Embed stylesheet only on admin settings page
		if( 'add-ons_page_themezee-widget-bundle' != $hook )
			return;
				
		// Enqueue Admin CSS
		wp_enqueue_style( 'tzwb-admin-stylesheet', TZWB_PLUGIN_URL . '/assets/css/tzwb-admin.css', array(), '20140604' );
		
	}
	
}

/* Run Admin Class */
TZWB_Settings_Page::setup();

endif;