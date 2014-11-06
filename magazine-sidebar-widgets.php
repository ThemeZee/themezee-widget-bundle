<?php
/*
Plugin Name: Magazine Sidebar Widgets
Plugin URI: http://wordpress.org/extend/plugins/magazine-sidebar-widgets/
Description: Registers a lean custom post type for slides and allow theme developers to built custom slideshows on top of it.
Author: ThemeZee
Author URI: http://themezee.com/
Version: 1.0
Text Domain: magazine-sidebar-widgets
Domain Path: /languages/
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Lean Custom Post Types Plugin
Copyright(C) 2014, ThemeZee.com - contact@themezee.com

*/


 /* Use class to avoid namespace collisions */
if ( ! class_exists('Magazine_Sidebar_Widgets') ) :

class Magazine_Sidebar_Widgets {

	/* Setup the Business Content Widgets plugin */
	static function setup() {

		/* Include Widget Classes */
		require( dirname( __FILE__ ) . '/widgets/widget-button-text.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-featured-page.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-popular-posts.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-recent-comments.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-recent-posts.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-social-icons.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-tabbed-content.php' );
		
		/* Translations only needed on backend. */
		if ( is_admin() ) :
			load_plugin_textdomain( 'magazine-sidebar-widgets', false, dirname(plugin_basename(__FILE__)) );
		endif;

		/* Register all widgets. */
		add_action( 'widgets_init',  array( __CLASS__, 'register_widgets' ) );

		/* Enqueue Frontend Widget Styles */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		
		// Enqueue Scripts and Styles on widgets admin screen
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_scripts' ) );

	}

	/* Register Widgets */
	static function register_widgets() {

		register_widget('MSW_Button_Text_Widget');
		register_widget('MSW_Featured_Page_Widget');
		register_widget('MSW_Popular_Posts_Widget');
		register_widget('MSW_Recent_Comments_Widget');
		register_widget('MSW_Recent_Posts_Widget');
		register_widget('MSW_Social_Icons_Widget');
		register_widget('MSW_Tabbed_Content_Widget');
		
	}
	
	/* Enqueue Widget Styles */
	static function enqueue_styles() {
	
		// Load stylesheet only if widgets are active
		if ( is_active_widget('MSW_Button_Widget', false, 'msw_button')
			or is_active_widget('MSW_Featured_Page_Widget', false, 'msw_featured_page')
			or is_active_widget('MSW_Popular_Posts_Widget', false, 'msw_popular_posts')
			or is_active_widget('MSW_Recent_Comments_Widget', false, 'msw_recent_comments')
			or is_active_widget('MSW_Recent_Posts_Widget', false, 'msw_recent_posts')
			or is_active_widget('MSW_Social_Icons_Widget', false, 'msw_social_icons')
			or is_active_widget('MSW_Tabbed_Content_Widget', false, 'msw_tabbed_content')
		) :
		
			// Enqueue BCW Plugin Stylesheet
			wp_enqueue_style('magazine-sidebar-widgets', self::get_stylesheet() );

		endif;
		
	}
	
	/* Get Stylesheet URL */
	static function get_stylesheet() {
		
		if ( file_exists( get_stylesheet_directory() . '/css/magazine-sidebar-widgets.css' ) )
			$stylesheet = get_stylesheet_directory() . '/css/magazine-sidebar-widgets.css';
		elseif ( file_exists( get_template_directory() . '/css/magazine-sidebar-widgets.css' ) )
			$stylesheet = get_template_directory() . '/css/magazine-sidebar-widgets.css';
		else 
			$stylesheet = plugins_url('/css/magazine-sidebar-widgets.css', __FILE__ );
		
		return $stylesheet;
	}
	
	/* Enqueue Backend Scripts and Styles */
	static function enqueue_admin_scripts( $hook ) {
		
		// Embed scripts only on widget page
		if( 'widgets.php' != $hook )
			return;
		
		// Enqueue Admin CSS
		wp_enqueue_style( 'msw-admin-stylesheet', plugins_url('/css/msw-admin.css', __FILE__ ), array(), '20140604' );

				
				
		// Enqueue Image Uploader
		#wp_enqueue_media();
		#wp_enqueue_script( 'msw-image-uploader-script', plugins_url('/js/image-uploader.js', __FILE__ ), array( 'media-views', 'customize-controls', 'underscore' ), '20140604', true );
		#wp_enqueue_style( 'msw-image-uploader-stylesheet', plugins_url('/css/image-uploader.css', __FILE__ ), array(), '20140604' );
		
		// Enqueue Dashicons Picker
		#wp_enqueue_script( 'msw-dashicons-picker-script', plugins_url('/js/dashicons-picker.js', __FILE__ ), array( 'jquery' ), '20140604', true );
		#wp_enqueue_style( 'msw-dashicons-picker-stylesheet', plugins_url('/css/dashicons-picker.css', __FILE__ ), array(), '20140604' );

	}
	
}

/* Run Plugin */
Magazine_Sidebar_Widgets::setup();

endif;
?>