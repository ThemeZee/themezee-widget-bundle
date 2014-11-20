<?php
/**
 * Registers the TZWB Plugin settings Page
 *
 */

 /* Use class to avoid namespace collisions */
if ( ! class_exists('TZWB_Admin') ) :

class TZWB_Admin {

	/* Setup the TZWB settings page */
	static function setup() {
		
		/* Register admin settings */
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		
		/* Add settings page to admin menu */
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ), 8 );

		/* Enqueue Admin Page Styles */
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_styles' ) );

	}
	
	/* Return TZWB options */
	static function get_options() {
		
		// Merge Plugin Options Array from Database with Default Options Array
		$plugin_options = wp_parse_args( 
			
			// Get saved theme options from WP database
			get_option( 'themezee_widget_bundle' , array() ), 
			
			// Merge with Default Options if setting was not saved yet
			 TZWB_Admin::default_options()
			
		);

		// Return theme options
		return $plugin_options;
		
	}
	
	/* Default TZWB options */
	static function default_options() {

		$default_options = array(
			'enable_widget_bgcolor' => true,
			'enable_widget_visibility' => false,
			'active_widgets' => array(
				'enable_tzwb_author_posts' => true,
				'enable_tzwb_category_posts' => true,
				'enable_tzwb_popular_posts' => true,
				'enable_tzwb_recent_comments' => true,
				'enable_tzwb_recent_posts' => true,
				'enable_tzwb_social_icons' => true,
				'enable_tzwb_tabbed_content' => true
				),
		);
		
		return $default_options;
	}
	
	/* Return Widget Array */
	static function widget_array() {

		$widgets = array(	
			'tzwb_author_posts' => array(
				'id' =>	'enable_tzwb_author_posts',
				'name' => __('Enable Author Posts Widget', 'themezee-widget-bundle'),	
				'description' => __('Displays recents posts from a chosen author.', 'themezee-widget-bundle') ),
			'tzwb_category_posts' => array(
				'id' =>	'enable_tzwb_category_posts',
				'name' => __('Enable Category Posts Widget', 'themezee-widget-bundle'),	
				'description' => __('Displays recents posts from a chosen category.', 'themezee-widget-bundle') ),
			'tzwb_popular_posts' => array(
				'id' =>	'enable_tzwb_popular_posts',
				'name' => __('Enable Popular Posts Widget', 'themezee-widget-bundle'),	
				'description' => __('Displays popular posts by comment count.', 'themezee-widget-bundle') ),
			'tzwb_recent_comments' => array(
				'id' =>	'enable_tzwb_recent_comments',
				'name' => __('Enable Recent Comments Widget', 'themezee-widget-bundle'),	
				'description' => __('Displays latest comments with Gravatar.', 'themezee-widget-bundle') ),
			'tzwb_recent_posts' => array(
				'id' =>	'enable_tzwb_recent_posts',
				'name' => __('Enable Recent Posts Widget', 'themezee-widget-bundle'),	
				'description' => __('Displays recents posts.', 'themezee-widget-bundle') ),
			'tzwb_social_icons' => array(
				'id' =>	'enable_tzwb_social_icons',
				'name' => __('Enable Social Icons Widget', 'themezee-widget-bundle'),	
				'description' => __('Displays your Social Icons Menu.', 'themezee-widget-bundle') ),
			'tzwb_tabbed_content' => array(
				'id' =>	'enable_tzwb_tabbed_content',
				'name' => __('Enable Tabbed Content Widget', 'themezee-widget-bundle'),	
				'description' => __('Displays various content with tabs.', 'themezee-widget-bundle') )
		);

		return $widgets;
	}
	
	/* Register Admin Settings */
	static function register_settings() {
	
		/* Register Settings */
		register_setting( 'themezee_widget_bundle', 'themezee_widget_bundle',  array( __CLASS__, 'validate_settings' ) );
	
		/* Add Settings Sections */
		add_settings_section( 'general', __('General', 'themezee-widget-bundle' ), '__return_false', 'themezee_widget_bundle' );
		add_settings_section( 'widgets', __('Widgets', 'themezee-widget-bundle'), '__return_false', 'themezee_widget_bundle' );
		
		/* Add Settings for General Section */
		$setting = array(
			'id' =>	'enable_widget_bgcolor',
			'title' => __('Backend Styles', 'themezee-widget-bundle'),
			'name' => __('Enable Highlighting of ThemeZee Widgets', 'themezee-widget-bundle'),	
			'description' => __('Adds an orange background color for all custom widgets of ThemeZee Widget Bundle on Appearance > Widgets.', 'themezee-widget-bundle'),
			'section' => 'general');
		add_settings_field( $setting['id'], $setting['title'] , array(__CLASS__,'display_checkbox_setting_field'), 'themezee_widget_bundle', $setting['section'], $setting);
		
		$setting = array(
			'id' =>	'enable_widget_visibility',
			'title' => __('Visibility', 'themezee-widget-bundle'),
			'name' => __('Enable Widget Visibility Feature', 'themezee-widget-bundle'),	
			'description' => __('Adds a "Visibility" tab to widget settings to set conditions where the widget should be displayed.', 'themezee-widget-bundle'),
			'section' => 'general');
		add_settings_field( $setting['id'], $setting['title'] , array(__CLASS__,'display_checkbox_setting_field'), 'themezee_widget_bundle', $setting['section'], $setting);
		
		
		/* Add Settings for Widget Section */
		$setting = array(
			'id' =>	'active_widgets',
			'title' => __('Active Widgets', 'themezee-widget-bundle'),
			'section' => 'widgets');
		add_settings_field( $setting['id'], $setting['title'] , array(__CLASS__,'display_widgets_setting_field'), 'themezee_widget_bundle', $setting['section']);
		
	}
	
	/* Display Checkbox Setting Field */
	static function display_checkbox_setting_field( $args = array() ) {
	
		$options = TZWB_Admin::get_options();
		
		?>
		<input name="themezee_widget_bundle[<?php echo $args['id']; ?>]" type="checkbox" id="tzwb_setting_<?php echo $args['id']; ?>" value="1" <?php checked( 1, $options[$args['id']] ); ?> />
		<label for="tzwb_setting_<?php echo $args['id']; ?>"><?php echo $args['name']; ?></label>
		<?php if( isset($args['description']) and $args['description'] <> '' ) : ?>
		<p class="description"><?php echo $args['description']; ?></p>
        <?php
		endif;
	}
	
	/* Display Active Widgets Setting Field */
	static function display_widgets_setting_field( $args = array() ) {
	
		$options = TZWB_Admin::get_options();
		$widgets = TZWB_Admin::widget_array();
		
		foreach ( $widgets as $widget ) : ?>
		
			<div class="tzwb-admin-active-widget-setting">
				<input name="themezee_widget_bundle[active_widgets][<?php echo $widget['id']; ?>]" type="checkbox" id="tzwb_setting_active_widgets_<?php echo $widget['id']; ?>" value="1" <?php checked( 1, (bool)$options['active_widgets'][$widget['id']] ); ?> />
				<label for="tzwb_setting_active_widgets_<?php echo $widget['id']; ?>"><?php echo $widget['name']; ?></label>
				<?php if( isset($widget['description']) and $widget['description'] <> '' ) : ?>
				<p class="description"><?php echo $widget['description']; ?></p>
				<?php endif; ?>
			</div>
	<?php	
		endforeach;
	}
	
	/* Validate and Save Settings */
	public function validate_settings( $settings ) {
		
		/* Validate General Settings */
		$settings['enable_widget_bgcolor'] = !empty($settings['enable_widget_bgcolor']);
		
		/* Validate Widget Settings */
		$widgets = TZWB_Admin::widget_array();
		foreach ( $widgets as $widget ) :
			$settings['active_widgets'][$widget['id']] = !empty($settings['active_widgets'][$widget['id']]);
		endforeach;
		
		return $settings;
	}
	
	/* Add Settings Page to Admin menu */
	static function add_settings_page() {
			
		add_menu_page(
			__( 'ThemeZee Add-ons', 'themezee-widget-bundle' ),
			__( 'Add-ons', 'themezee-widget-bundle' ),
			'manage_options',
			'themezee-add-ons',
			array( __CLASS__, 'display_settings_page' ),
			'dashicons-index-card'
		);
		add_submenu_page(
			'themezee-add-ons',
			__( 'ThemeZee Add-ons', 'themezee-widget-bundle' ),
			__( 'Overview', 'themezee-widget-bundle' ),
			'manage_options',
			'themezee-add-ons',
			array( __CLASS__, 'display_settings_page' )
		);

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
	
		$options = TZWB_Admin::get_options();

	?>
		
		<div id="tzwb-admin-wrap" class="wrap">
			
			<h2><?php _e( 'ThemeZee Widget Bundle Settings', 'themezee-widget-bundle' ); ?></h2>
			<?php settings_errors(); ?>

			<div id="tzwb-admin-container" class="tzwb-admin-clearfix">
			
				<div id="tzwb-admin-content">

					<form method="post" action="options.php">
						<?php
							settings_fields('themezee_widget_bundle');
							do_settings_sections('themezee_widget_bundle');
							submit_button();
						?>
					</form>
					
				</div>
				
				<div id="tzwb-admin-sidebar" class="tzwb-admin-clearfix">
					<?php TZWB_Admin::display_sidebar(); ?>
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
		wp_enqueue_style( 'tzwb-admin-stylesheet', plugins_url('/css/tzwb-admin.css', dirname( __FILE__ ) ), array(), '20140604' );
		
	}
	
}

/* Run Admin Class */
TZWB_Admin::setup();

endif;
?>