<?php
/**
 * Registers the MSW Plugin settings Page
 *
 */

 /* Use class to avoid namespace collisions */
if ( ! class_exists('MSW_Admin') ) :

class MSW_Admin {

	/* Setup the MSW settings page */
	static function setup() {
		
		/* Register admin settings */
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		
		/* Add settings page to admin menu */
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ), 8 );

		/* Enqueue Frontend Widget Styles */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );

	}
	
	/* Return MSW options */
	static function get_options() {
		
		// Merge Plugin Options Array from Database with Default Options Array
		$plugin_options = wp_parse_args( 
			
			// Get saved theme options from WP database
			get_option( 'magazine_sidebar_widgets' , array() ), 
			
			// Merge with Default Options if setting was not saved yet
			 MSW_Admin::default_options()
			
		);

		// Return theme options
		return $plugin_options;
		
	}
	
	/* Default MSW options */
	static function default_options() {

		$default_options = array(
			'enable_css' 							=> true,
			'enable_msw_author_posts' 				=> true,
		);
		
		return $default_options;
	}
	
	/* Register Admin Settings */
	static function register_settings() {
	
		/* Add Settings Sections */
		add_settings_section( 'general', __('General', 'magazine-sidebar-widgets' ), '__return_false', 'magazine_sidebar_widgets' );
		add_settings_section( 'widgets', __('Widgets', 'magazine-sidebar-widgets'), '__return_false', 'magazine_sidebar_widgets' );
		add_settings_section( 'addons', __('Addons', 'magazine-sidebar-widgets'), '__return_false', 'magazine_sidebar_widgets' );
		
		/* Add Settings for General Section */
		$setting = array(
			'id' =>	'enable_css',
			'title' => __('Stylesheet', 'magazine-sidebar-widgets'),
			'name' => __('Enable "theme-my-login.css"', 'magazine-sidebar-widgets'),	
			'description' => __('In order to keep changes between upgrades, you can store your customized "theme-my-login.css" in your current theme directory.', 'magazine-sidebar-widgets'),
			'section' => 'general');
		add_settings_field( $setting['id'], $setting['title'] , array(__CLASS__,'display_checkbox_field'), 'magazine_sidebar_widgets', $setting['section'], $setting);
		
		/* Add Settings for Widget Section */
		$setting = array(
			'id' =>	'enable_msw_author_posts',
			'title' => __('Author Posts', 'magazine-sidebar-widgets'),
			'name' => __('Enable "Author Posts Widget"', 'magazine-sidebar-widgets'),	
			'description' => __('Displays recents posts from a chosen author.', 'magazine-sidebar-widgets'),
			'section' => 'widgets');
		add_settings_field( $setting['id'], $setting['title'] , array(__CLASS__,'display_checkbox_field'), 'magazine_sidebar_widgets', $setting['section'], $setting);
		
		
		register_setting( 'magazine_sidebar_widgets', 'magazine_sidebar_widgets',  array( __CLASS__, 'validate_settings' ) );
		
	}
	
	static function display_checkbox_field( $args = array() ) {
	
		$options = MSW_Admin::get_options();
		
		?>
		<input name="magazine_sidebar_widgets[<?php echo $args['id']; ?>]" type="checkbox" id="msw_setting_<?php echo $args['id']; ?>" value="1" <?php checked( 1, $options[$args['id']] ); ?> />
		<label for="msw_setting_<?php echo $args['id']; ?>"><?php echo $args['name']; ?></label>
		<p class="description"><?php echo $args['description']; ?></p>
        <?php
	}
	
	/* Validate and Save Settings */
	public function validate_settings( $settings ) {
		
		/* Validate General Settings */
		$settings['enable_css'] = isset($settings['enable_css']);
		
		/* Validate Widget Settings */
		$settings['enable_msw_author_posts'] = isset($settings['enable_msw_author_posts']);
		
		return $settings;
	}
	
	/* Add Settings Page to Admin menu */
	static function add_settings_page() {
		
		add_options_page(
			__( 'Magazine Sidebar Widget Settings', 'magazine-sidebar-widgets' ),
			__( 'Magazine Sidebar Widgets', 'magazine-sidebar-widgets' ),
			'manage_options',
			'magazine_sidebar_widgets',
			array( __CLASS__, 'display_settings_page' )
		);
		
	}
	
	/* Display Settings Page */
	static function display_settings_page() { 
	
		$options = MSW_Admin::get_options();
		
		var_dump($options);
	?>
		
		<div id="msw-settings-page" class="wrap">
			
			<h2><?php _e( 'Magazine Sidebar Widget Settings', 'magazine-sidebar-widgets' ); ?></h2>

			<form method="post" action="options.php">
				<?php
					settings_fields('magazine_sidebar_widgets');
					do_settings_sections('magazine_sidebar_widgets');
					submit_button();
				?>
			</form>
			
		</div>
<?php	
	}

	/* Enqueue Admin Styles */
	static function enqueue_styles() {
		
		// Enqueue BCW Plugin Stylesheet
		#wp_enqueue_style('magazine-sidebar-widgets', self::get_stylesheet() );
		
	}
	
	
}

/* Run Admin Class */
MSW_Admin::setup();

endif;
?>