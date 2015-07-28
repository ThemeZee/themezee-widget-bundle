<?php
/*
Plugin Name: ThemeZee Widget Bundle
Plugin URI: http://themezee.com/addons/widget-bundle/
Description: Includes several new custom sidebar widgets to show your best content and information.
Author: ThemeZee
Author URI: http://themezee.com/
Version: 1.0
Text Domain: themezee-widget-bundle
Domain Path: /languages/
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Lean Custom Post Types Plugin
Copyright(C) 2014, ThemeZee.com - contact@themezee.com

*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Use class to avoid namespace collisions
if ( ! class_exists('ThemeZee_Widget_Bundle') ) :

/**
 * Main ThemeZee_Widget_Bundle Class
 *
 * @since 1.0
 */
class ThemeZee_Widget_Bundle {

	/**
	 * ThemeZee Widget Bundle Setup
	 *
	 * Calls all Functions to setup the Plugin
	 *
	 * @since 1.0
	 * @static
	 * @uses ThemeZee_Widget_Bundle::constants() Setup the constants needed
	 * @uses ThemeZee_Widget_Bundle::includes() Include the required files
	 * @uses ThemeZee_Widget_Bundle::setup_actions() Setup the hooks and actions
	 * @uses ThemeZee_Widget_Bundle::updater() Setup the plugin updater
	 */
	static function setup() {
	
		// Setup Constants
		self::constants();
		
		// Include Files
		self::includes();
		
		// Setup Action Hooks
		self::setup_actions();
		
		// Load Translation File
		load_plugin_textdomain( 'themezee-widget-bundle', false, dirname(plugin_basename(__FILE__)) );

	}
	
	
	/**
	 * Setup plugin constants
	 *
	 * @since 1.0
	 * @return void
	 */
	static function constants() {
		
		// Define Plugin Name
		define( 'TZWB_NAME', 'ThemeZee Widget Bundle');

		// Define Version Number
		define( 'TZWB_VERSION', '1.0' );

		// Define Update API URL
		define( 'TZWB_STORE_API_URL', 'https://themezee.com' ); 

		// Plugin Folder Path
		define( 'TZWB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		// Plugin Folder URL
		define( 'TZWB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		// Plugin Root File
		define( 'TZWB_PLUGIN_FILE', __FILE__ );
		
	}
	
	/**
	 * Include required files
	 *
	 * @since 1.0
	 * @return void
	 */
	static function includes() {

		// Include Admin Classes
		require_once TZWB_PLUGIN_DIR . '/includes/admin/class-themezee-addons-overview.php';
		require_once TZWB_PLUGIN_DIR . '/includes/admin/class-tzwb-plugin-updater.php';
		
		// Include Settings Classes
		require_once TZWB_PLUGIN_DIR . '/includes/settings/class-tzwb-settings.php';
		require_once TZWB_PLUGIN_DIR . '/includes/settings/class-tzwb-settings-page.php';
		
		// Include Widget Classes
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-author-posts.php';
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-category-posts.php';
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-popular-posts.php';
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-recent-comments.php';
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-recent-posts.php';
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-social-icons.php';
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-tabbed-content.php';
		
	}
	
	
	/**
	 * Setup Action Hooks
	 *
	 * @since 1.0
	 * @return void
	 */
	static function setup_actions() {

		// Register all widgets
		add_action( 'widgets_init',  array( __CLASS__, 'register_widgets' ) );

		// Enqueue Frontend Widget Styles
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		
		// Enqueue Scripts and Styles on widgets admin screen
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_scripts' ) );
		
		// Include Widget Visibility Class if Jetpack is not active
		add_action( 'init',  array( __CLASS__, 'widget_visibility_class' ), 11 );
		
		// Add Widget Bundle Box to Add-on Overview Page
		add_action('themezee_addons_overview_page', array( __CLASS__, 'addon_overview_page' ) );
		
		// Add automatic plugin updater from ThemeZee Store API
		add_action( 'admin_init', array( __CLASS__, 'plugin_updater' ), 0 );
		
	}

	/* Register Widgets */
	static function register_widgets() {
		
		$options = TZWB_Settings::get_options();
		
		if( isset($options['active_widgets']['tzwb_author_posts']) and $options['active_widgets']['tzwb_author_posts'] == true) :
			register_widget('TZWB_Author_Posts_Widget');
		endif;
		
		if( isset($options['active_widgets']['tzwb_category_posts']) and $options['active_widgets']['tzwb_category_posts'] == true) :
			register_widget('TZWB_Category_Posts_Widget');
		endif;
		
		if( isset($options['active_widgets']['tzwb_popular_posts']) and $options['active_widgets']['tzwb_popular_posts'] == true) :
			register_widget('TZWB_Popular_Posts_Widget');
		endif;
		
		if( isset($options['active_widgets']['tzwb_recent_comments']) and $options['active_widgets']['tzwb_recent_comments'] == true) :
			register_widget('TZWB_Recent_Comments_Widget');
		endif;
		
		if( isset($options['active_widgets']['tzwb_recent_posts']) and $options['active_widgets']['tzwb_recent_posts'] == true) :
			register_widget('TZWB_Recent_Posts_Widget');
		endif;
		
		if( isset($options['active_widgets']['tzwb_social_icons']) and $options['active_widgets']['tzwb_social_icons'] == true) :
			register_widget('TZWB_Social_Icons_Widget');
		endif;
		
		if( isset($options['active_widgets']['tzwb_tabbed_content']) and $options['active_widgets']['tzwb_tabbed_content'] == true) :
			register_widget('TZWB_Tabbed_Content_Widget');
		endif;
		
	}
	
	/* Enqueue Widget Styles */
	static function enqueue_styles() {
	
		// Load stylesheet only if widgets are active
		if ( is_active_widget('TZWB_Author_Posts_Widget', false, 'tzwb_author_posts')
			or is_active_widget('TZWB_Category_Posts_Widget', false, 'tzwb_category_posts')
			or is_active_widget('TZWB_Popular_Posts_Widget', false, 'tzwb_popular_posts')
			or is_active_widget('TZWB_Recent_Comments_Widget', false, 'tzwb_recent_comments')
			or is_active_widget('TZWB_Recent_Posts_Widget', false, 'tzwb_recent_posts')
			or is_active_widget('TZWB_Social_Icons_Widget', false, 'tzwb_social_icons')
			or is_active_widget('TZWB_Tabbed_Content_Widget', false, 'tzwb_tabbed_content')
		) :
		
			// Enqueue BCW Plugin Stylesheet
			wp_enqueue_style('themezee-widget-bundle', self::get_stylesheet() );

		endif;
		
	}
	
	/* Get Stylesheet URL */
	static function get_stylesheet() {
		
		if ( file_exists( get_stylesheet_directory() . '/css/themezee-widget-bundle.css' ) )
			$stylesheet = get_stylesheet_directory() . '/css/themezee-widget-bundle.css';
		elseif ( file_exists( get_template_directory() . '/css/themezee-widget-bundle.css' ) )
			$stylesheet = get_template_directory() . '/css/themezee-widget-bundle.css';
		else 
			$stylesheet = TZWB_PLUGIN_URL . '/assets/css/themezee-widget-bundle.css';
		
		return $stylesheet;
	}
	
	/* Enqueue Backend Scripts and Styles */
	static function enqueue_admin_scripts( $hook ) {
		
		// Embed Widget Highlight only on widget page
		if( 'widgets.php' != $hook )
			return;
	
		wp_enqueue_style( 'tzwb-widget-bgcolor', TZWB_PLUGIN_URL . '/assets/css/tzwb-widget-bgcolor.css', array(), '20140604' );
		
	}
	
	/* Enqueue Widget Visibility Class */
	static function widget_visibility_class() {
		
		// Do not run when Jetpack is active
		if ( class_exists( 'Jetpack_Widget_Conditions' ) )
			return;
		
		// Get Plugin Options
		$options = TZWB_Settings::get_options();
		
		// Include Widget Visibility class
		if( $options['widget_visibility'] == true ) :
			require TZWB_PLUGIN_DIR . '/includes/admin/class-tzwb-widget-visibility.php';
		endif;
		
	}
	
	
	static function addon_overview_page() { 
	
		$plugin_data = get_plugin_data( __FILE__ );
		
		?>

		<dl><dt><h4><?php echo esc_html( $plugin_data['Name'] ); ?> <?php echo esc_html( $plugin_data['Version'] ); ?></h4></dt>
			<dd>
				<p>
					<?php echo wp_kses_post( $plugin_data['Description'] ); ?><br/>
				</p>
				<p>
					<a href="<?php echo admin_url( 'admin.php?page=themezee-widget-bundle' ); ?>" class="button button-primary"><?php _e('Plugin Settings', 'themezee-widget-bundle'); ?></a> 
					<a href="<?php echo admin_url( 'plugins.php?s=ThemeZee+Widget+Bundle' ); ?>" class="button button-secondary"><?php _e('Deactivate', 'themezee-widget-bundle'); ?></a>
				</p>
				
			</dd>
		</dl>
		
		<?php
	}
	
	
	/**
	 * Plugin Updater
	 *
	 * @since 1.0
	 * @return void
	 */
	static function plugin_updater() {

		if( ! is_admin() ) :
			return;
		endif;
		
		$options = TZWB_Settings::get_options();

		if( isset($options['license_key']) and $options['license_key'] <> '') :
			
			$license_key = $options['license_key'];
			
			// setup the updater
			$tzwb_updater = new TZWB_Plugin_Updater( TZWB_STORE_API_URL, __FILE__, array(
					'version' 	=> TZWB_VERSION,
					'license' 	=> $license_key,
					'item_name' => TZWB_NAME,
					'item_id'   => 41305,
					'author' 	=> 'ThemeZee'
				)
			);
			
		endif;
		
	}
	
}

/* Run Plugin */
ThemeZee_Widget_Bundle::setup();

endif;