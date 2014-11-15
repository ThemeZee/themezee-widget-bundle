<?php

// Recent Posts Widget
class MSW_Tabbed_Content_Widget extends WP_Widget {

	function __construct() {
		
		// Setup Widget
		$widget_ops = array(
			'classname' => 'msw_tabbed_content', 
			'description' => __('Displays various content with tabs.', 'magazine-sidebar-widgets')
		);
		$control_ops = array(
			'width' => 450, 
			'id_base' => 'msw_tabbed_content'
		);
		
		$this->WP_Widget('msw_tabbed_content', 'Tabbed Content (ThemeZee)', $widget_ops, $control_ops);
		
		// Enqueue Javascript for Tabs
		if ( is_active_widget(false, false, $this->id_base) )
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts') );
		
		// Delete Widget Cache on certain actions
		add_action( 'save_post', array( $this, 'delete_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'delete_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'delete_widget_cache' ) );
		add_action( 'comment_post', array( $this, 'delete_widget_cache' ) );
		add_action( 'transition_comment_status', array( $this, 'delete_widget_cache' ) );
		
	}
	
	public function enqueue_scripts() {

		wp_enqueue_script('msw-tabbed-content', plugins_url('/js/tabbed-content.js', dirname( __FILE__ ) ), array('jquery'));
		
	}
	
	public function delete_widget_cache() {
		
		delete_transient( $this->id );
		
	}
	
	private function default_settings() {
	
		$defaults = array(
			'title'				=> '',
			'number'			=> 5,
			'thumbnails'		=> true,
			'tab_titles'		=> array('', '', '', ''),
			'tab_content'		=> array(0, 0, 0, 0)
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
		<div class="msw-tabbed-content">
		
			<?php // Display Title
			if( !empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }; ?>
			
			<div class="msw-content msw-clearfix">
				
				<?php echo $this->render($args, $instance); ?>
				
			</div>
			
		</div>
	<?php
		echo $after_widget;
	
	}
	
	// Render Widget Content
	function render($args, $instance) {
		
		// Get Output from Cache
		$output = get_transient( $this->id );
		$output = false;
		
		// Generate output if not cached
		if( $output === false ) :

			// Get Widget Settings
			$defaults = $this->default_settings();
			extract( wp_parse_args( $instance, $defaults ) );

			// Start Output Buffering
			ob_start();
				
			?>
			
			<div class="msw-tabnavi-wrap msw-clearfix">
			
				<ul class="msw-tabnavi">
				
					<?php // Display Tab Titles
					for( $i = 0; $i <= 3; $i++ ) : 

						// Do not display empty tabs
						if ( $tab_titles[$i] == '' and $tab_content[$i] == 0)
							continue;
					?>
					
						<li><a href="#<?php echo $args['widget_id']; ?>-tab-<?php echo $i; ?>"><?php echo esc_html($tab_titles[$i]); ?></a></li>
						
					<?php endfor; ?>
					
				</ul>
				
			</div>
				
			<?php // Display Tab Content
			for( $i = 0; $i <= 3; $i++ ) : ?>
				
					<div id="<?php echo $args['widget_id']; ?>-tab-<?php echo $i; ?>" class="msw-tabcontent">
					
						<?php echo $this->tab_content($instance, $tab_content[$i]); ?>
						
					</div>
					
			<?php endfor; ?>

	<?php
			// Get Buffer Content
			$output = ob_get_clean();
			
			// Set Cache
			set_transient( $this->id, $output, YEAR_IN_SECONDS );
			
		endif;
		
		return $output;
		
	}
	
	// Display Tab Content
	function tab_content($instance, $tabcontent) {
	
		// Get Widget Settings
		$defaults = $this->default_settings();
		extract( wp_parse_args( $instance, $defaults ) );

		switch($tabcontent) :

			 // Archives
			 case 1: ?>
			
				<ul class="msw-tabcontent-archives">
					<?php wp_get_archives( array('type' => 'monthly', 'show_post_count' => 1) ); ?>
				</ul>
			
			<?php
			break;
			
			// Categories
			case 2:  ?>
			
				<ul class="msw-tabcontent-categories">
					<?php wp_list_categories( array('title_li' => '', 'orderby' => 'name', 'show_count' => 1, 'hierarchical' => false) ); ?>
				</ul>
			
			<?php
			break;
			
			// Pages
			 case 3: ?>
			
				<ul class="msw-tabcontent-pages">
					<?php wp_list_pages( array('title_li' => '') ); ?>
				</ul>
			
			<?php
			break;
			
			// Popular Posts
			case 4:  
			
				// Get latest popular posts from database
				$query_arguments = array(
					'posts_per_page' => (int)$number,
					'ignore_sticky_posts' => true,
					'orderby' => 'comment_count'
				);
				$posts_query = new WP_Query($query_arguments);
			?>
			
				<ul class="msw-tabcontent-popular-posts msw-posts-list">
					
					<?php // Display Posts
					if( $posts_query->have_posts() ) : while( $posts_query->have_posts() ) : $posts_query->the_post();
					
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
							if ( $thumbnails == 1 ) : ?>
								
								<span class="msw-meta-date"><?php the_time(get_option('date_format')); ?></span>

							<?php endif; ?>
							
							</div>
						
					<?php endwhile; 
					endif; ?>
					
				</ul>
			
			<?php
			break;

			// Recent Comments
			case 5: 
			
				// Get latest comments from database
				$comments = get_comments( array( 
					'number' => (int)$number, 
					'status' => 'approve', 
					'post_status' => 'publish' 
				) );
			?>
			
				<ul class="msw-tabcontent-comments msw-comments-list">
					
					<?php // Display Comments
					if ( $comments ) :
						foreach ( (array) $comments as $comment) :
					
							 // Display Gravatar
							if ( $thumbnails == 1 ) : ?>
						
								<li class="msw-has-avatar">
									<a href="<?php echo esc_url( get_comment_link($comment->comment_ID) ); ?>">
										<?php echo get_avatar( $comment, 55 ); ?>
									</a>
						
							<?php else: ?>
								
								<li>
								
							<?php endif;
							
							echo get_comment_author_link($comment->comment_ID);
							_e(' on', 'magazine-sidebar-widgets'); ?>
						
							<a href="<?php echo esc_url( get_comment_link($comment->comment_ID) ); ?>">
								<?php echo get_the_title($comment->comment_post_ID); ?>
							</a>
							
					<?php endforeach;
					endif; ?>
					
				</ul>
			
			<?php
			break;
			
			case 9: // Recent Comments
				global $comments, $comment;
				$comments = get_comments( apply_filters( 'msw_widget_tabbed_comments_args', array( 'number' => $this->number, 'status' => 'approve', 'post_status' => 'publish' ) ) );
				$content = '<ul class="widget-tabbed-comments">';
				if ( $comments ) {
					foreach ( (array) $comments as $comment) {
						if ( $this->thumbs == 1 ) : // add avatar
							$content .= '<li class="widget-avatar"><a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">' . get_avatar( $comment, 55 ) . '</a>';
						else:
							$content .=  '<li>';
						endif;
						$content .=  sprintf(_x('%1$s on %2$s', 'widgets', 'magazine-sidebar-widgets'), get_comment_author_link(), '<a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
					}
				}
				$content .= '</ul>';
			break;

			// Recent Posts
			case 6:  
			
				// Get latest posts from database
				$query_arguments = array(
					'posts_per_page' => (int)$number,
					'ignore_sticky_posts' => true
				);
				$posts_query = new WP_Query($query_arguments);
			?>
			
				<ul class="msw-tabcontent-recent-posts msw-posts-list">
					
					<?php // Display Posts
					if( $posts_query->have_posts() ) : while( $posts_query->have_posts() ) : $posts_query->the_post();
					
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
							if ( $thumbnails == 1 ) : ?>
								
								<span class="msw-meta-date"><?php the_time(get_option('date_format')); ?></span>

							<?php endif; ?>
							
							</div>
						
					<?php endwhile; 
					endif; ?>
					
				</ul>
			
			<?php
			break;

			// Tag Cloud
			case 7: ?>
			
				<div class="msw-tabcontent-tagcloud tagcloud">
					<?php wp_tag_cloud( array('taxonomy' => 'post_tag') ); ?>
				</div>
			
			<?php
			break;

			// No Content selected
			default: ?>
				
				<p class="msw-tabcontent-missing">
					<?php _e('Please select the Tab Content in the Widget Settings.', 'magazine-sidebar-widgets'); ?>
				</p>
			
			<?php
			break;

		
		endswitch;
	}

	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['number'] = (int)$new_instance['number'];
		$instance['thumbnails'] = !empty($new_instance['thumbnails']);
		
		// Validate Tab Settings
		$instance['tab_content'] = array();
		$instance['tab_titles'] = array();
		
		for( $i = 0; $i <= 3; $i++ ) :
		
			$instance['tab_content'][$i] = (int)$new_instance['tab_content-'.$i];
			$instance['tab_titles'][$i] = sanitize_text_field($new_instance['tab_titles-'.$i]);
			
		endfor;
		
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
		
		
		<div style="background: #f5f5f5; padding: 3px 10px; margin-bottom: 10px;">
			
		<?php // Display Tab Options
		for( $i = 0; $i <= 3; $i++ ) : ?>
					
			<p>
				<label for="<?php echo $this->get_field_id('tab_content-'.$i); ?>">
					<?php printf( __( 'Tab %s:', 'magazine-sidebar-widgets' ), $i+1 ); ?>
				</label>
				<select id="<?php echo $this->get_field_id('tab_content-'.$i); ?>" name="<?php echo $this->get_field_name('tab_content-'.$i); ?>">
					<option value="0" <?php selected($tab_content[$i], 0); ?>></option>
					<option value="1" <?php selected($tab_content[$i], 1); ?>><?php _e('Archives', 'magazine-sidebar-widgets'); ?></option>
					<option value="2" <?php selected($tab_content[$i], 2); ?>><?php _e('Categories', 'magazine-sidebar-widgets'); ?></option>
					<option value="3" <?php selected($tab_content[$i], 3); ?>><?php _e('Pages', 'magazine-sidebar-widgets'); ?></option>
					<option value="4" <?php selected($tab_content[$i], 4); ?>><?php _e('Popular Posts', 'magazine-sidebar-widgets'); ?></option>
					<option value="5" <?php selected($tab_content[$i], 5); ?>><?php _e('Recent Comments', 'magazine-sidebar-widgets'); ?></option>
					<option value="6" <?php selected($tab_content[$i], 6); ?>><?php _e('Recent Posts', 'magazine-sidebar-widgets'); ?></option>
					<option value="7" <?php selected($tab_content[$i], 7); ?>><?php _e('Tag Cloud', 'magazine-sidebar-widgets'); ?></option>
				</select>
				
				<label for="<?php echo $this->get_field_id('tab_titles-'.$i); ?>"><?php _e('Title:', 'magazine-sidebar-widgets'); ?>
					<input id="<?php echo $this->get_field_id('tab_titles-'.$i); ?>" name="<?php echo $this->get_field_name('tab_titles-'.$i); ?>" type="text" value="<?php echo $tab_titles[$i]; ?>" />
				</label>
			</p>
						
		<?php endfor; ?>
				
		</div>
		
		<strong><?php _e('Settings for Recent/Popular Posts and Recent Comments', 'magazine-sidebar-widgets'); ?></strong>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of entries:', 'magazine-sidebar-widgets'); ?>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('thumbnails'); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $thumbnails ) ; ?> id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" />
				<?php _e('Show Thumbnails?', 'magazine-sidebar-widgets'); ?>
			</label>
		</p>
<?php
	}
}

?>