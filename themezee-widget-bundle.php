<?php
/*
Plugin Name: ThemeZee Widget Bundle
Plugin URI: http://themezee.com/addons/widget-bundle/
Description: A collection of our most popular widgets, neatly bundled into a single plugin. The Plugin includes advanced widgets for Recent Posts, Recent Comments, Facebook Likebox, Tabbed Content, Social Icons and more.
Author: ThemeZee
Author URI: http://themezee.com/
Version: 1.0
Text Domain: themezee-widget-bundle
Domain Path: /languages/
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

ThemeZee Widget Bundle
Copyright(C) 2015, ThemeZee.com - support@themezee.com

*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Use class to avoid namespace collisions
if ( ! class_exists('ThemeZee_Widget_Bundle') ) :


/**
 * Main ThemeZee_Widget_Bundle Class
 *
 * @package ThemeZee Widget Bundle
 */
class ThemeZee_Widget_Bundle {

	/**
	 * Call all Functions to setup the Plugin
	 *
	 * @uses ThemeZee_Widget_Bundle::constants() Setup the constants needed
	 * @uses ThemeZee_Widget_Bundle::includes() Include the required files
	 * @uses ThemeZee_Widget_Bundle::setup_actions() Setup the hooks and actions
	 * @return void
	 */
	static function setup() {
	
		// Setup Constants
		self::constants();
		
		// Setup Translation
		add_action( 'plugins_loaded', array( __CLASS__, 'translation' ) );
	
		// Include Files
		self::includes();
		
		// Setup Action Hooks
		self::setup_actions();
		
	}
	
	
	/**
	 * Setup plugin constants
	 *
	 * @return void
	 */
	static function constants() {
		
		// Define Plugin Name
		define( 'TZWB_NAME', 'ThemeZee Widget Bundle');

		// Define Version Number
		define( 'TZWB_VERSION', '1.0' );
		
		// Define Plugin Name
		define( 'TZWB_PRODUCT_ID', 41305);

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
	 * Load Translation File
	 *
	 * @return void
	 */
	static function translation() {

		load_plugin_textdomain( 'themezee-widget-bundle', false, dirname( plugin_basename( TZWB_PLUGIN_FILE ) ) . '/languages/' );
		
	}
	
	
	/**
	 * Include required files
	 *
	 * @return void
	 */
	static function includes() {

		// Include Admin Classes
		require_once TZWB_PLUGIN_DIR . '/includes/class-themezee-addons-page.php';
		require_once TZWB_PLUGIN_DIR . '/includes/class-tzwb-plugin-updater.php';
		
		// Include Settings Classes
		require_once TZWB_PLUGIN_DIR . '/includes/settings/class-tzwb-settings.php';
		require_once TZWB_PLUGIN_DIR . '/includes/settings/class-tzwb-settings-page.php';
		
		// Include Widget Classes
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-facebook-likebox.php';
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-recent-comments.php';
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-recent-posts.php';
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-social-icons.php';
		require_once TZWB_PLUGIN_DIR . '/includes/widgets/widget-tabbed-content.php';
		
	}
	
	
	/**
	 * Setup Action Hooks
	 *
	 * @see https://codex.wordpress.org/Function_Reference/add_action WordPress Codex
	 * @return void
	 */
	static function setup_actions() {

		// Register all widgets
		add_action( 'widgets_init',  array( __CLASS__, 'register_widgets' ) );

		// Enqueue Frontend Widget Styles
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		
		// Enqueue Scripts and Styles on widgets admin screen
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_scripts' ) );
		
		// Register Image Sizes
		add_action( 'init',  array( __CLASS__, 'add_image_size' ) );

		// Add Widget Bundle Box to Add-on Overview Page
		add_action('themezee_addons_overview_page', array( __CLASS__, 'addon_overview_page' ) );
		
		// Add automatic plugin updater from ThemeZee Store API
		add_action( 'admin_init', array( __CLASS__, 'plugin_updater' ), 0 );
		
	}


	/**
	 * Register Widgets
	 *
	 * @return void
	 */
	static function register_widgets() {
		
		// Get Settings
		$instance = TZWB_Settings::instance();
		$options = $instance->get_all();
		
		// Register Widgets if enabled
		if( true == $options['facebook_likebox'] ) :
			register_widget( 'TZWB_Facebook_Likebox_Widget' );
		endif;
		
		if( true ==  $options['recent_comments'] ) :
			register_widget( 'TZWB_Recent_Comments_Widget' );
		endif;
		
		if( true ==  $options['recent_posts'] ) :
			register_widget( 'TZWB_Recent_Posts_Widget' );
		endif;
		
		if( true ==  $options['social_icons'] ) :
			register_widget( 'TZWB_Social_Icons_Widget' );
		endif;
		
		if( true ==  $options['tabbed_content'] ) :
			register_widget( 'TZWB_Tabbed_Content_Widget' );
		endif;
		
	}
	
	
	/**
	 * Enqueue Widget Styles
	 *
	 * @return void
	 */
	static function enqueue_styles() {
		
		// Return early if theme handles styling
		if ( current_theme_supports( 'themezee-widget-bundle' ) ) :
			return;
		endif;
		
		// Load stylesheet only if widgets are active
		if ( is_active_widget( 'TZWB_Facebook_Likebox_Widget', false, 'tzwb-facebook-likebox' )
			or is_active_widget( 'TZWB_Recent_Comments_Widget', false, 'tzwb-recent-comments' )
			or is_active_widget( 'TZWB_Recent_Posts_Widget', false, 'tzwb-recent-posts' )
			or is_active_widget( 'TZWB_Social_Icons_Widget', false, 'tzwb-social-icons' )
			or is_active_widget( 'TZWB_Tabbed_Content_Widget', false, 'tzwb-tabbed-content' )
		) :
		
			// Enqueue Plugin Stylesheet
			wp_enqueue_style( 'themezee-widget-bundle', TZWB_PLUGIN_URL . 'assets/css/themezee-widget-bundle.css', array(), TZWB_VERSION );
			
			// Enqueue Genericons
			wp_enqueue_style( 'genericons', TZWB_PLUGIN_URL . 'assets/genericons/genericons.css', array(), TZWB_VERSION );

		endif;
		
	}
	
	
	/**
	 * Enqueue Admin Styles
	 *
	 * @return void
	 */
	static function enqueue_admin_scripts( $hook ) {
		
		// Embed Widget Highlight only on widget page
		if( 'widgets.php' != $hook ) :
			return;
		endif;
	
		wp_enqueue_style( 'tzwb-widget-bgcolor', TZWB_PLUGIN_URL . 'assets/css/tzwb-widget-bgcolor.css', array(), TZWB_VERSION );
		
	}
	
	
	/**
	 * Add custom image size for post thumbnails in widgets
	 *
	 * @return void
	 */
	static function add_image_size() {
		
		// Return early if theme handles image sizes
		if ( current_theme_supports( 'themezee-widget-bundle' ) ) :
			return;
		endif;
		
		add_image_size( 'tzwb-thumbnail', 80, 80, true );
		
	}
	
	
	/**
	 * Add widget bundle box to addon overview admin page
	 *
	 * @return void
	 */
	static function addon_overview_page() { 
	
		$plugin_data = get_plugin_data( __FILE__ );
		
		?>

		<dl>
			<dt>
				<h4><?php echo esc_html( $plugin_data['Name'] ); ?></h4>
				<span><?php printf( esc_html__( 'Version %s', 'themezee-widget-bundle'),  esc_html( $plugin_data['Version'] ) ); ?></span>
			</dt>
			<dd>
				<p><?php echo wp_kses_post( $plugin_data['Description'] ); ?><br/></p>
				<a href="<?php echo admin_url( 'admin.php?page=themezee-addons&tab=widgets' ); ?>" class="button button-primary"><?php esc_html_e('Plugin Settings', 'themezee-widget-bundle'); ?></a>&nbsp;
				<a href="<?php echo esc_url( 'http://themezee.com/docs/widget-bundle/'); ?>" class="button button-secondary" target="_blank"><?php esc_html_e('View Documentation', 'themezee-widget-bundle'); ?></a>
			</dd>
		</dl>
		
		<?php
	}
	
	
	/**
	 * Plugin Updater
	 *
	 * @return void
	 */
	static function plugin_updater() {

		if( ! is_admin() ) :
			return;
		endif;
		
		$options = TZWB_Settings::instance();

		if( $options->get('license_key') <> '') :
			
			$license_key = $options->get('license_key');
			
			// setup the updater
			$tzwb_updater = new TZWB_Plugin_Updater( TZWB_STORE_API_URL, __FILE__, array(
					'version' 	=> TZWB_VERSION,
					'license' 	=> $license_key,
					'item_name' => TZWB_NAME,
					'item_id'   => TZWB_PRODUCT_ID,
					'author' 	=> 'ThemeZee'
				)
			);
			
		endif;
		
	}
	
}

// Run Plugin
ThemeZee_Widget_Bundle::setup();

endif;