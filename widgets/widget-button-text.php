<?php

// Dashicons Icon with Text Widget
class MSW_Button_Text_Widget extends WP_Widget {

	function __construct() {

		// Setup Widget
		$widget_ops = array(
			'classname' => 'msw_button_text', 
			'description' => __('Displays a neat Icon and Text.', 'magazine-sidebar-widgets') 
		);
		$this->WP_Widget('msw_button_text', __('Text & Button Widget (ThemeZee)', 'magazine-sidebar-widgets'), $widget_ops);
		
	}
	
	private function default_settings() {
	
		$defaults = array(
			'title'		=> '',
			'text'		=> '',
			'link_url' 	=> '',
			'link_text'	=> __('Button Text', 'magazine-sidebar-widgets')
		);
		
		return $defaults;
		
	}

	function widget($args, $instance) {

		extract($args);
		
		// Get Widget Settings
		$defaults = $this->default_settings();
		extract( wp_parse_args( $instance, $defaults ) );
		
		// Filter Widget Title
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		
		// Output
		echo $before_widget;
	?>
		<div class="msw-button-text">
			
			<div class="msw-content">
				
				<?php // Display Title
				if ( !empty( $title ) ) { echo $before_title . esc_attr($title) . $after_title; } ?>
				
				<div class="msw-entry">
					
					<?php // Display Text
					echo $text; ?>
				
				</div>
				
				<?php // Display Call to Action Button
				if( $link_url != '' && $link_text != '' ) : ?>
					<a href="<?php echo esc_url($link_url); ?>" class="msw-button msw-call-to-action-button"><?php echo esc_attr($link_text); ?></a>
				<?php endif; ?>
				
			</div>
			
		</div>
	<?php
		echo $after_widget;
	
	}

	function update($new_instance, $old_instance) {
	
		$instance = $old_instance;
		$instance['title'] = esc_attr( $new_instance['title'] );
		$instance['link_url'] = esc_url( $new_instance['link_url'] );
		$instance['link_text'] = esc_attr( $new_instance['link_text'] );
		
		if ( current_user_can('unfiltered_html') ) :
			$instance['text'] =  $new_instance['text'];
		else :
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) );
		endif;
		
		return $instance;
	}

	function form($instance) {
		
		// Get Widget Settings
		$defaults = $this->default_settings();
		extract( wp_parse_args( $instance, $defaults ) );

	?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'magazine-sidebar-widgets'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:', 'magazine-sidebar-widgets'); ?></label>
			<textarea class="widefat" rows="3" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_textarea($text); ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link_url'); ?>"><?php _e('Call to Action URL:', 'magazine-sidebar-widgets'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('link_url'); ?>" name="<?php echo $this->get_field_name('link_url'); ?>" type="text" value="<?php echo esc_attr($link_url); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link_text'); ?>"><?php _e('Call to Action Button Text:', 'magazine-sidebar-widgets'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" type="text" value="<?php echo esc_attr($link_text); ?>" />
		</p>
	<?php
	}
}


?>