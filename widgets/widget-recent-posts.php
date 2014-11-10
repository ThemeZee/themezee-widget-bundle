<?php

// Recent Posts Widget
class MSW_Recent_Posts_Widget extends WP_Widget {

	function __construct() {
		
		// Setup Widget
		$widget_ops = array(
			'classname' => 'msw_recent_posts', 
			'description' => __('Displays recent posts.', 'magazine-sidebar-widgets')
		);
		$this->WP_Widget('msw_recent_posts', 'Recent Posts (ThemeZee)', $widget_ops);
		
		// Delete Widget Cache on certain actions
		add_action( 'save_post', array( $this, 'delete_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'delete_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'delete_widget_cache' ) );
		
	}

	function excerpt_length($length) {
		return $this->excerpt_length;
	}
	
	public function delete_widget_cache() {
		
		delete_transient( $this->id );
		
	}
	
	private function default_settings() {
	
		$defaults = array(
			'title'				=> '',
			'number'			=> 5,
			'thumbnails'		=> true,
			'excerpt_length' 	=> 0,
			'postmeta'			=> false
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
		<div class="msw-recent-posts msw-posts">
		
			<?php // Display Title
			if( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }; ?>
			
			<div class="msw-content msw-clearfix">
				
				<ul class="msw-posts-list">
					<?php echo $this->render($instance); ?>
				</ul>
				
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
		
			// Get latest popular posts from database
			$query_arguments = array(
				'posts_per_page' => (int)$number,
				'ignore_sticky_posts' => true,
				'orderby' => 'comment_count'
			);
			$posts_query = new WP_Query($query_arguments);

			// Start Output Buffering
			ob_start();
			
			// Check if there are posts
			if( $posts_query->have_posts() ) :
			
				// Limit the number of words for the excerpt
				$this->excerpt_length = (int)$excerpt_length;
				add_filter('excerpt_length', array(&$this, 'excerpt_length') );	
				
				// Display Posts
				while( $posts_query->have_posts() ) :
					
					$posts_query->the_post(); 
					
					if ( $thumbnails == 1 ) : ?>
				
						<li class="msw-has-thumbnail">
							<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
								<?php the_post_thumbnail('msw-thumbnail'); ?>
							</a>
				
					<?php else: ?>
						
						<li>
						
					<?php endif; ?>
				
						<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
							<?php if ( get_the_title() ) the_title(); else the_ID(); ?>
						</a>

					<?php // Display Post Content
					if ( $excerpt_length > 0 ) : ?>
						
						<span class="msw-excerpt"><?php the_excerpt(); ?></span>
					
					<?php endif; ?>

					<?php // Display Postmeta
					if ( $postmeta == 1 ) : ?>
						
						<div class="msw-postmeta">
							<span class="msw-meta-date"><?php the_time(get_option('date_format')); ?></span>
							<span class="msw-meta-comment"><a href="<?php the_permalink() ?>#comments"><?php comments_number(__('No comments', 'magazine-sidebar-widgets'),__('One comment','magazine-sidebar-widgets'),__('% comments','magazine-sidebar-widgets')); ?></a></span>
						</div>
						
					<?php endif; ?>
					
				<?php
				endwhile;
				
				// Remove excerpt filter
				remove_filter('excerpt_length', array(&$this, 'excerpt_length') );	
				
			endif;
			
			// Reset Postdata
			wp_reset_postdata();
			
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
		$instance['number'] = (int)$new_instance['number'];
		$instance['thumbnails'] = isset($new_instance['thumbnails']);
		$instance['excerpt_length'] = (int)$new_instance['excerpt_length'];
		$instance['postmeta'] = isset($new_instance['postmeta']);
		
		$this->delete_widget_cache();
		
		return $instance;
	}

	function form( $instance ) {
		
		// Get Widget Settings
		$defaults = $this->default_settings();
		extract( wp_parse_args( $instance, $defaults ) );

?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'magazine-sidebar-widgets'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:', 'magazine-sidebar-widgets'); ?>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('thumbnails'); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $thumbnails ) ; ?> id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" />
				<?php _e('Show Post Thumbnails?', 'magazine-sidebar-widgets'); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('excerpt_length'); ?>"><?php _e('Excerpt length in number of words:', 'magazine-sidebar-widgets'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" type="text" value="<?php echo $excerpt_length; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('postmeta'); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $postmeta ) ; ?> id="<?php echo $this->get_field_id('postmeta'); ?>" name="<?php echo $this->get_field_name('postmeta'); ?>" />
				<?php _e('Show Postmeta(Date, Comments)?', 'magazine-sidebar-widgets'); ?>
			</label>
		</p>
<?php
	}
}

?>