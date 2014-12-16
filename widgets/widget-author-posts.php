<?php

// Popular Posts Widget
class TZWB_Author_Posts_Widget extends WP_Widget {

	function __construct() {
		
		// Setup Widget
		$widget_ops = array(
			'classname' => 'tzwb_author_posts', 
			'description' => __('Displays recents posts from a chosen author.', 'themezee-widget-bundle')
		);
		$this->WP_Widget('tzwb_author_posts', 'Author Posts (ThemeZee)', $widget_ops);
		
		// Delete Widget Cache on certain actions
		add_action( 'save_post', array( $this, 'delete_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'delete_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'delete_widget_cache' ) );
		
	}
	
	public function delete_widget_cache() {
		
		wp_cache_delete('widget_tzwb_author_posts', 'widget');
		
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

		// Get Widget Object Cache
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_tzwb_author_posts', 'widget' );
		}
		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		// Display Widget from Cache if exists
		if ( isset( $cache[ $this->id ] ) ) {
			echo $cache[ $this->id ];
			return;
		}
		
		// Start Output Buffering
		ob_start();
		
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
		<div class="tzwb-author-posts tzwb-posts">
		
			<?php // Display Title
			if( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }; ?>
			
			<div class="tzwb-content tzwb-clearfix">
				
				<ul class="tzwb-posts-list">
					<?php echo $this->render($instance); ?>
				</ul>
				
			</div>
			
		</div>
	<?php
		echo $after_widget;
		
		// Set Cache
		if ( ! $this->is_preview() ) {
			$cache[ $this->id ] = ob_get_flush();
			wp_cache_set( 'widget_tzwb_author_posts', $cache, 'widget' );
		} else {
			ob_end_flush();
		}
	
	}
	
	// Render Widget Content
	function render($instance) {
		
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

		// Check if there are posts
		if( $posts_query->have_posts() ) :
		
			// Display Posts
			while( $posts_query->have_posts() ) :
				
				$posts_query->the_post(); 
				
				if ( $thumbnails == 1 ) : ?>
			
					<li class="tzwb-has-thumbnail">
						<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
							<?php the_post_thumbnail('tzwb-thumbnail'); ?>
						</a>
			
				<?php else: ?>
					
					<li>
					
				<?php endif; ?>
			
					<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
						<?php if ( get_the_title() ) the_title(); else the_ID(); ?>
					</a>


					<div class="tzwb-postmeta">
						
					<?php // Display Date
					if ( $meta_date == 1 ) : ?>
						
						<span class="tzwb-meta-date"><?php the_time(get_option('date_format')); ?></span>
						
					<?php endif; ?>
					
					<?php // Display Comments
					if ( $meta_comments == 1 and comments_open() ) : ?>
					
						<span class="tzwb-meta-comments">
							<?php comments_popup_link( __('No comments', 'themezee-widget-bundle'),__('One comment','themezee-widget-bundle'),__('% comments','themezee-widget-bundle') ); ?>
						</span>
						
					<?php endif; ?>
					
					</div>
				
			<?php
			endwhile;
			
		endif;
		
		// Reset Postdata
		wp_reset_postdata();
		
	}

	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['number'] = (int)$new_instance['number'];
		$instance['author'] = (int)$new_instance['author'];
		$instance['thumbnails'] = !empty($new_instance['thumbnails']);
		$instance['meta_date'] = !empty($new_instance['meta_date']);
		$instance['meta_comments'] = !empty($new_instance['meta_comments']);
		
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
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:', 'themezee-widget-bundle'); ?>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('author'); ?>"><?php _e('Select Author:', 'themezee-widget-bundle'); ?></label><br/>
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
				<?php _e('Show Post Thumbnails?', 'themezee-widget-bundle'); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('meta_date'); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $meta_date ) ; ?> id="<?php echo $this->get_field_id('meta_date'); ?>" name="<?php echo $this->get_field_name('meta_date'); ?>" />
				<?php _e('Show Post Date?', 'themezee-widget-bundle'); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('meta_comments'); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $meta_comments ) ; ?> id="<?php echo $this->get_field_id('meta_comments'); ?>" name="<?php echo $this->get_field_name('meta_comments'); ?>" />
				<?php _e('Show Post Comments?', 'themezee-widget-bundle'); ?>
			</label>
		</p>
<?php
	}
}

?>