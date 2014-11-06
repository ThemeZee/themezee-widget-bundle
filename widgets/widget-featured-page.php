<?php

// Featured Page Widget class
class MSW_Featured_Page_Widget extends WP_Widget {

	function __construct() {
		
		// Setup Widget
		$widget_ops = array(
			'classname' => 'msw_featured_page', 
			'description' => __('Displays thumbnail and excerpt of a selected page.', 'magazine-sidebar-widgets')
		);
		$this->WP_Widget('msw_featured_page', 'Featured Page (ThemeZee)', $widget_ops);

	}
	
	function excerpt_length($length) {
		return $this->excerpt_length;
	}
	
	private function default_settings() {
	
		$defaults = array(
			'title'				=> '',
			'page_id'			=> 0,
			'excerpt_length'	=> 0,
			'image_size' 		=> 'full',
			'read_more'			=> __('Read more', 'magazine-sidebar-widgets')
		);
		
		return $defaults;
		
	}

	function widget($args, $instance) {

		extract($args);
		
		// Get Widget Settings
		$defaults = $this->default_settings();
		extract( wp_parse_args( $instance, $defaults ) );
		
		// Get Page Content
		global $post;
		$post = get_post($page_id); 
		
		// Setup Post Data
		setup_postdata( $post ); 
		
		// Limit the number of words for the excerpt
		$this->excerpt_length = (int)$excerpt_length;
		add_filter('excerpt_length', array(&$this, 'excerpt_length') );	

		// Output
		echo $before_widget;
	?>
		<div class="msw-featured-page">
			
			<a href="<?php esc_url(the_permalink()) ?>" class="msw-featured-image">			
				<?php // Display Featured Image
					the_post_thumbnail($image_size); ?>
			</a>
			
			<?php // Display Title
			if( !empty( $title ) ) { echo $before_title . $title . $after_title; }; ?>
			
			<div class="msw-entry textwidget">
				
				<?php the_excerpt(); ?>
				
				<?php // Display Read more Button
				if( $read_more != '' ) : ?>
					<a href="<?php esc_url(the_permalink()) ?>" class="msw-button"><?php echo $read_more; ?></a>
				<?php endif; ?>
			</div>
			
		</div>
	<?php
		echo $after_widget;
		
		// Remove excerpt filter
		remove_filter('excerpt_length', array(&$this, 'excerpt_length') );	

	}

	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['page_id'] = (int)$new_instance['page_id'];
		$instance['excerpt_length'] = (int)$new_instance['excerpt_length'];
		$instance['image_size'] = esc_attr($new_instance['image_size']);
		$instance['read_more'] = esc_attr($new_instance['read_more']);
		
		return $instance;
	}

	function form($instance) {
		
		// Get Widget Settings
		$defaults = $this->default_settings();
		extract( wp_parse_args( $instance, $defaults ) );

	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'magazine-sidebar-widgets'); ?> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('page_id'); ?>"><?php _e('Select Featured Page:', 'magazine-sidebar-widgets'); ?><br/>
			
				<?php // Show Dropdown Categories
				$dropdown_pages_args = array( 
					'id' => $this->get_field_id('page_id'), 
					'name' => $this->get_field_name('page_id'), 
					'show_option_none' => ' ', 
					'selected' => $page_id
				);
				wp_dropdown_pages($dropdown_pages_args); ?>
			
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('excerpt_length'); ?>"><?php _e('Excerpt length in number of words:', 'magazine-sidebar-widgets'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" type="text" value="<?php echo $excerpt_length; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Featured Image Size:', 'magazine-sidebar-widgets'); ?><br/>
				
				<?php $image_sizes = $this->get_image_sizes(); ?>
				
				<select name="<?php echo $this->get_field_name('image_size'); ?>" id="<?php echo $this->get_field_id('image_size'); ?>">
					
					<option value="full" <?php selected( $image_size, 'full' ); ?>><?php _e('Full Image Size', 'magazine-sidebar-widgets'); ?></option>

					<?php foreach ($image_sizes as $size_name => $size_attrs): ?>
						
						<option value="<?php echo $size_name ?>" <?php selected( $image_size, $size_name ); ?>><?php echo $size_name ?> <?php printf( __('(%1$s x %2$s pixel)', 'magazine-sidebar-widgets'), $size_attrs['width'], $size_attrs['height'] ); ?></option>
					
					<?php endforeach; ?>
					
				</select>
			
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('read_more'); ?>"><?php _e('Read More button text (Leave blank for no button):', 'magazine-sidebar-widgets'); ?> 
				<input class="widefat" id="<?php echo $this->get_field_id('read_more'); ?>" name="<?php echo $this->get_field_name('read_more'); ?>" type="text" value="<?php echo esc_attr($read_more); ?>" />
			</label>
		</p>
	<?php
	}
	
	function get_image_sizes( $size = '' ) {

        global $_wp_additional_image_sizes;

        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {

                if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

                        $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                        $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                        $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

                } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                        $sizes[ $_size ] = array( 
                                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
                        );

                }

        }

        // Get only 1 size if found
        if ( $size ) {

                if( isset( $sizes[ $size ] ) ) {
                        return $sizes[ $size ];
                } else {
                        return false;
                }

        }

        return $sizes;
	}
}
?>