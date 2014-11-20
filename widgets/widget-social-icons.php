<?php

// Social Icons Widget
class TZWB_Social_Icons_Widget extends WP_Widget {

	function __construct() {
		
		// Setup Widget
		$widget_ops = array(
			'classname' => 'tzwb_social_icons', 
			'description' => __('Displays your Social Icons.', 'themezee-widget-bundle')
		);
		$this->WP_Widget('tzwb_social_icons', 'Social Icons (ThemeZee)', $widget_ops);
		
		// Delete Widget Cache on certain actions
		add_action( 'wp_update_nav_menu', array( $this, 'delete_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'delete_widget_cache' ) );
		
	}

	public function delete_widget_cache() {
		
		delete_transient( $this->id );
		
	}
	
	private function default_settings() {
	
		$defaults = array(
			'title'				=> '',
			'menu'				=> 0,
			'style'				=> 'icons'
		);
		
		return $defaults;
		
	}
	
	// Display Widget
	function widget($args, $instance) {

		// Get Sidebar Arguments
		extract($args);
		
		// Get Widget Settings
		$defaults = $this->default_settings();
		extract( wp_parse_args( $instance, $defaults ) );
		
		// Add Widget Title Filter
		$widget_title = apply_filters('widget_title', $title, $instance, $this->id_base);
		
		// Output
		echo $before_widget;
	?>
		<div class="tzwb-social-icons">
		
			<?php // Display Title
			if( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }; ?>
			
			<div class="tzwb-content tzwb-clearfix">
				
				<?php echo $this->render($instance); ?>
				
			</div>
			
		</div>
	<?php
		echo $after_widget;
	
	}
	
	// Render Widget Content
	function render($instance) {
		
		// Get Output from Cache
		$output = get_transient( $this->id );
		
		// Generate output if not cached
		if( $output === false ) :

			// Get Widget Settings
			$defaults = $this->default_settings();
			extract( wp_parse_args( $instance, $defaults ) );

			// Start Output Buffering
			ob_start();
			
			// Check if there is a social_icons menu
			if( isset($menu) and $menu > 0 ) :
			
				// Set Social Menu Arguments
				$menu_args = array(
					'menu' => (int)$menu,
					'container' => false,
					'menu_class' => 'tzwb-social-icons-menu',
					'echo' => true,
					'fallback_cb' => '',
					'before' => '',
					'after' => '',
					'link_before' => '<span class="screen-reader-text">',
					'link_after' => '</span>',
					'depth' => 1
				);
				
				// Display Social Icons Menu
				wp_nav_menu( $menu_args );
				
			else: // Display Hint how to configure Social Icons ?>

				<p class="tzwb-social-icons-hint">
					<?php _e('Please go to WP-Admin -> Appearance -> Menus and create a new custom menu with custom links to all your social networks. Then select your created menu on the "Social Icons" widget settings.', 'themezee-widget-bundle'); ?>
				</p>
				
		<?php
			endif;

			
			// Get Buffer Content
			$output = ob_get_clean();
			
			// Set Cache
			set_transient( $this->id, $output, YEAR_IN_SECONDS );
			
		endif;
		
		return $output;
		
	}

	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['menu'] = (int)$new_instance['menu'];
		$instance['style'] = esc_attr($new_instance['style']);
		
		$this->delete_widget_cache();
		
		return $instance;
	}

	function form( $instance ) {
		
		// Get Widget Settings
		$defaults = $this->default_settings();
		extract( wp_parse_args( $instance, $defaults ) );

?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'themezee-widget-bundle'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('menu'); ?>"><?php _e('Select Social Menu:', 'themezee-widget-bundle'); ?></label><br/>
			<select id="<?php echo $this->get_field_id('menu'); ?>" name="<?php echo $this->get_field_name('menu'); ?>">
				<option value="0" <?php selected($menu, 0, false); ?>> </option>
				<?php // Display Menu Select Options
					$nav_menus = wp_get_nav_menus(array('hide_empty' => true));
					
					foreach ( $nav_menus as $nav_menu ) :
						printf('<option value="%s" %s>%s</option>', $nav_menu->term_id, selected($menu, $nav_menu->term_id, false), $nav_menu->name);
					endforeach;
				?>
			</select>
		</p>

<?php
	}
}

?>