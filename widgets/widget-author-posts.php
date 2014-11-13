<?php

// Popular Posts Widget
class MSW_Author_Posts_Widget extends WP_Widget {

	function __construct() {
		
		// Setup Widget
		$widget_ops = array(
			'classname' => 'msw_author_posts', 
			'description' => __('Displays recents posts from a chosen author.', 'magazine-sidebar-widgets')
		);
		$this->WP_Widget('msw_author_posts', 'Author Posts (ThemeZee)', $widget_ops);
		
		// Delete Widget Cache on certain actions
		add_action( 'save_post', array( $this, 'delete_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'delete_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'delete_widget_cache' ) );
		
	}
	
	public function delete_widget_cache() {
		
		delete_transient( $this->id );
		
	}
	
	private function default_settings() {
	
		$defaults = array(
			'title'				=> '',
			'number'			=> 5,
			'author'			=> 0,
			'thumbnails'		=> true,
			'meta_date'			=> false,
			'meta_comments'		=> false
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
		<div class="msw-author-posts msw-posts">
		
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
				'author' => (int)$author
			);
			$posts_query = new WP_Query($query_arguments);

			// Start Output Buffering
			ob_start();
			
			// Check if there are posts
			if( $posts_query->have_posts() ) :
			
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


						<div class="msw-postmeta">
							
						<?php // Display Date
						if ( $meta_date == 1 ) : ?>
							
							<span class="msw-meta-date"><?php the_time(get_option('date_format')); ?></span>
							
						<?php endif; ?>
						
						<?php // Display Comments
						if ( $meta_comments == 1 and comments_open() ) : ?>
						
							<span class="msw-meta-comments">
								<?php comments_popup_link( __('No comments', 'magazine-sidebar-widgets'),__('One comment','magazine-sidebar-widgets'),__('% comments','magazine-sidebar-widgets') ); ?>
							</span>
							
						<?php endif; ?>
						
						</div>
					
				<?php
				endwhile;
				
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
		$instance['author'] = (int)$new_instance['author'];
		$instance['thumbnails'] = isset($new_instance['thumbnails']);
		$instance['meta_date'] = isset($new_instance['meta_date']);
		$instance['meta_comments'] = isset($new_instance['meta_comments']);
		
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
			<label for="<?php echo $this->get_field_id('author'); ?>"><?php _e('Select Author:', 'magazine-sidebar-widgets'); ?></label><br/>
			<select id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>">
				<option value="0" <?php selected($author, 0, false); ?>> </option>
				<?php // Display Author Select Options
					$users = get_users(array('who' => 'authors'));
					
					foreach ( $users as $user ) :
						printf('<option value="%s" %s>%s</option>', $user->data->ID, selected($author, $user->data->ID, false), $user->data->display_name);
					endforeach;
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('thumbnails'); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $thumbnails ) ; ?> id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" />
				<?php _e('Show Post Thumbnails?', 'magazine-sidebar-widgets'); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('meta_date'); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $meta_date ) ; ?> id="<?php echo $this->get_field_id('meta_date'); ?>" name="<?php echo $this->get_field_name('meta_date'); ?>" />
				<?php _e('Show Post Date?', 'magazine-sidebar-widgets'); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('meta_comments'); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $meta_comments ) ; ?> id="<?php echo $this->get_field_id('meta_comments'); ?>" name="<?php echo $this->get_field_name('meta_comments'); ?>" />
				<?php _e('Show Post Comments?', 'magazine-sidebar-widgets'); ?>
			</label>
		</p>
<?php
	}
}

?>