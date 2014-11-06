<?php

// Recent Comments Widget
class MSW_Recent_Comments_Widget extends WP_Widget {

	function __construct() {
		
		// Setup Widget
		$widget_ops = array(
			'classname' => 'msw_recent_comments', 
			'description' => __('Displays latest comments with Gravatar.', 'magazine-sidebar-widgets')
		);
		$this->WP_Widget('msw_recent_comments', 'Recent Comments (ThemeZee)', $widget_ops);
		
		// Delete Widget Cache on certain actions
		add_action( 'comment_post', array( $this, 'delete_widget_cache' ) );
		add_action( 'transition_comment_status', array( $this, 'delete_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'delete_widget_cache' ) );
		
	}

	public function delete_widget_cache() {
		
		delete_transient( $this->id );
		
	}
	
	function comment_length($comment, $length = 0) {
		$parts = explode("\n", wordwrap($comment, $length, "\n"));
		return $parts[0];
	}
	
	private function default_settings() {
	
		$defaults = array(
			'title'				=> '',
			'number'			=> 5,
			'avatar'			=> true,
			'post_title' 		=> true,
			'comment_length'	=> 0,
			'comment_date'		=> false
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
		<div class="msw-recent-comments">
		
			<?php // Display Title
			if( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }; ?>
			
			<div class="msw-content msw-clearfix">
				
				<ul class="msw-comments-list">
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
		
			// Get latest comments from database
			$comments = get_comments( array( 
				'number' => (int)$number, 
				'status' => 'approve', 
				'post_status' => 'publish' 
			) );

			// Start Output Buffering
			ob_start();
			
			// Check if there are comments
			if ( $comments ) :

				// Display Comments
				foreach ( (array) $comments as $comment) : ?>
					
					<?php // Display Gravatar
					if ( $avatar == 1 ) : ?>
				
						<li class="msw-has-avatar">
							<a href="<?php echo esc_url( get_comment_link($comment->comment_ID) ); ?>">
								<?php echo get_avatar( $comment, 55 ); ?>
							</a>
				
					<?php else: ?>
						
						<li>
						
					<?php endif; ?>
					
					
					<?php // Display Post Title
					if ( $post_title == 1 ) : 
				
						echo get_comment_author_link($comment->comment_ID);
						_e(' on', 'magazine-sidebar-widgets'); ?>
						
						<a href="<?php echo esc_url( get_comment_link($comment->comment_ID) ); ?>">
							<?php echo get_the_title($comment->comment_post_ID); ?>
						</a>

					<?php else: ?>
						
						<a href="<?php echo esc_url( get_comment_link($comment->comment_ID) ); ?>">
							<?php echo get_comment_author_link($comment->comment_ID); ?>
						</a>

					<?php endif; ?>
					
					
					<?php // Display Comment Content
					if ( $comment_length > 0 ) :  ?>
						
						<div class="msw-comment-content"><?php echo $this->comment_length($comment->comment_content, $comment_length); ?></div>

					<?php endif; ?>
					
					<?php // Display Comment Date
					if ( $comment_date == 1 ) : 

						$date_format = get_option( 'date_format' );
						$time_format = get_option( 'time_format' );
					?>
						
						<div class="msw-comment-date"><?php echo date($date_format . ' ' . $time_format , strtotime($comment->comment_date)); ?></div>

					<?php endif; ?>
					
				<?php
				endforeach;
				
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
		$instance['number'] = (int)$new_instance['number'];
		$instance['avatar'] = isset($new_instance['avatar']);
		$instance['post_title'] = isset($new_instance['post_title']);
		$instance['comment_length'] = (int)$new_instance['comment_length'];
		$instance['comment_date'] = isset($new_instance['comment_date']);
		
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
			<label for="<?php echo $this->get_field_id('avatar'); ?>">
				<input class="checkbox" type="checkbox"  <?php checked( $avatar ) ; ?> id="<?php echo $this->get_field_id('avatar'); ?>" name="<?php echo $this->get_field_name('avatar'); ?>" />
				<?php _e('Show avatar of comment author?', 'magazine-sidebar-widgets'); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('post_title'); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $post_title ) ; ?> id="<?php echo $this->get_field_id('post_title'); ?>" name="<?php echo $this->get_field_name('post_title'); ?>" />
				<?php _e('Show post title of commented post?', 'magazine-sidebar-widgets'); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('comment_length'); ?>">
				<?php _e('Comment Excerpt length in number of characters:', 'magazine-sidebar-widgets'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('comment_length'); ?>" name="<?php echo $this->get_field_name('comment_length'); ?>" type="text" value="<?php echo $comment_length; ?>" />
			</label>
		</p>	
		
		<p>
			<label for="<?php echo $this->get_field_id('comment_date'); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $comment_date ) ; ?> id="<?php echo $this->get_field_id('comment_date'); ?>" name="<?php echo $this->get_field_name('comment_date'); ?>" />
				<?php _e('Show date of comment?', 'magazine-sidebar-widgets'); ?>
			</label>
		</p>
		
<?php
	}
}

?>