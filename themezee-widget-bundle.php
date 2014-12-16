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


 /* Use class to avoid namespace collisions */
if ( ! class_exists('ThemeZee_Widget_Bundle') ) :

class ThemeZee_Widget_Bundle {

	/* Setup the ThemeZee Widget Bundle plugin */
	static function setup() {

		/* Include Admin settings page Classes */
		require( dirname( __FILE__ ) . '/admin/themezee-addons-overview.php' );
		require( dirname( __FILE__ ) . '/admin/tzwb-admin.php' );
		
		/* Include Widget Classes */
		require( dirname( __FILE__ ) . '/widgets/widget-author-posts.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-category-posts.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-popular-posts.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-recent-comments.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-recent-posts.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-social-icons.php' );
		require( dirname( __FILE__ ) . '/widgets/widget-tabbed-content.php' );
		
		/* Include Widget Visibility Class if Jetpack is not active */
		add_action( 'init',  array( __CLASS__, 'widget_visibility_class' ), 11 );

		/* Translations only needed on backend. */
		if ( is_admin() ) :
			load_plugin_textdomain( 'themezee-widget-bundle', false, dirname(plugin_basename(__FILE__)) );
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
		
		$options = TZWB_Admin::get_options();
		
		if( isset($options['active_widgets']['enable_tzwb_author_posts']) and $options['active_widgets']['enable_tzwb_author_posts'] == true) :
			register_widget('TZWB_Author_Posts_Widget');
		endif;
		
		if( isset($options['active_widgets']['enable_tzwb_category_posts']) and $options['active_widgets']['enable_tzwb_category_posts'] == true) :
			register_widget('TZWB_Category_Posts_Widget');
		endif;
		
		if( isset($options['active_widgets']['enable_tzwb_popular_posts']) and $options['active_widgets']['enable_tzwb_popular_posts'] == true) :
			register_widget('TZWB_Popular_Posts_Widget');
		endif;
		
		if( isset($options['active_widgets']['enable_tzwb_recent_comments']) and $options['active_widgets']['enable_tzwb_recent_comments'] == true) :
			register_widget('TZWB_Recent_Comments_Widget');
		endif;
		
		if( isset($options['active_widgets']['enable_tzwb_recent_posts']) and $options['active_widgets']['enable_tzwb_recent_posts'] == true) :
			register_widget('TZWB_Recent_Posts_Widget');
		endif;
		
		if( isset($options['active_widgets']['enable_tzwb_social_icons']) and $options['active_widgets']['enable_tzwb_social_icons'] == true) :
			register_widget('TZWB_Social_Icons_Widget');
		endif;
		
		if( isset($options['active_widgets']['enable_tzwb_tabbed_content']) and $options['active_widgets']['enable_tzwb_tabbed_content'] == true) :
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
			$stylesheet = plugins_url('/css/themezee-widget-bundle.css', __FILE__ );
		
		return $stylesheet;
	}
	
	/* Enqueue Backend Scripts and Styles */
	static function enqueue_admin_scripts( $hook ) {
		
		// Embed Widget Highlight only on widget page
		if( 'widgets.php' != $hook )
			return;
		
		$options = TZWB_Admin::get_options();
			
		if( isset($options['enable_widget_bgcolor']) and $options['enable_widget_bgcolor'] == true) :
				
			wp_enqueue_style( 'tzwb-widget-bgcolor', plugins_url('/css/tzwb-widget-bgcolor.css', __FILE__ ), array(), '20140604' );
			
		endif;
		
	}
	
	/* Enqueue Widget Visibility Class */
	static function widget_visibility_class() {
		
		/* Do not run when Jetpack is active */
		if ( class_exists( 'Jetpack_Widget_Conditions' ) )
			return;
		
		$options = TZWB_Admin::get_options();
		
		/* Include Widget Visibility class */
		if( isset($options['enable_widget_visibility']) and $options['enable_widget_visibility'] == true) :
			require( dirname( __FILE__ ) . '/admin/tzwb-widget-visibility.php' );
		endif;
		
	}
	
}

/* Run Plugin */
ThemeZee_Widget_Bundle::setup();

endif;
?>