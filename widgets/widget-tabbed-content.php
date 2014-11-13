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

						// Only display title when tab content was selected
						if ( $tab_content[$i] == 0 )
							continue;
					?>
					
						<li><a href="#<?php echo $args['widget_id']; ?>-tab-<?php echo $i; ?>"><?php echo esc_html($tab_titles[$i]); ?></a></li>
						
					<?php endfor; ?>
					
				</ul>
				
			</div>
				
			<?php // Display Tab Content
			for( $i = 0; $i <= 3; $i++ ) : 

				// Only display title when tab content was selected
				if ( $tab_content[$i] == 0 )
					continue;
			?>
				
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
	function tab_content($instance, $tab) {
	
		// Get Widget Settings
		$defaults = $this->default_settings();
		extract( wp_parse_args( $instance, $defaults ) );

		switch($tab) {

			case 1: // Archives
				$content = '<ul>' . wp_get_archives(apply_filters('msw_widget_tabbed_archives_args', array('type' => 'monthly', 'show_post_count' => 1, 'echo' => 0))) . '</ul>';
			break;

			case 2: // Categories
				$cat_args = array('title_li' => '', 'orderby' => 'name', 'show_count' => 1, 'hierarchical' => false, 'echo' => 0);
				$content = '<ul>' . wp_list_categories(apply_filters('msw_widget_tabbed_categories_args', $cat_args)) . '</ul>';
			break;

			case 3: // Links
				$content = wp_list_bookmarks(apply_filters('msw_widget_tabbed_links_args', array(
						'title_li' => '', 'title_before' => '<span class="widget_links_cat">', 'title_after' => '</span>', 'category_before' => '',
						'category_after' => '', 'show_images' => false, 'show_description' => false, 'show_name' => true, 'show_rating' => false, 'echo' => 0)));
			break;

			case 4: // Meta
				$content = '<ul>' . wp_register('<li>', '</li>', false) . '<li>' . wp_loginout('', false) . '</li>';
				$content .= '<li><a href="'.get_bloginfo('rss2_url').'" title="'.esc_attr(__('Syndicate this site using RSS 2.0', 'magazine-sidebar-widgets')).'">'. __('Entries <abbr title="Really Simple Syndication">RSS</abbr>', 'magazine-sidebar-widgets').'</a></li>';
				$content .= '<li><a href="'.get_bloginfo('comments_rss2_url').'" title="'.esc_attr(__('The latest comments to all posts in RSS', 'magazine-sidebar-widgets')).'">'. __('Comments <abbr title="Really Simple Syndication">RSS</abbr>', 'magazine-sidebar-widgets').'</a></li>';
				$content .= '<li><a href="'.esc_url('http://wordpress.org/').'" title="'.esc_attr(__('Powered by WordPress, state-of-the-art semantic personal publishing platform.', 'magazine-sidebar-widgets')).'">'. __( 'WordPress.org', 'magazine-sidebar-widgets') .'</a></li>';
				$content .= wp_meta() . '</ul>';
			break;

			case 5: // Pages
				$content = '<ul>' . wp_list_pages( apply_filters('msw_widget_tabbed_pages_args', array('title_li' => '', 'echo' => 0) ) ) . '</ul>';
			break;

			case 6: // Popular Posts
				$posts = new WP_Query( apply_filters( 'msw_widget_tabbed_popular_posts_args', array( 'posts_per_page' => $this->number, 'orderby' => 'comment_count', 'order' => 'DESC', 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
				if ($posts->have_posts()) :
					$content = '<ul>';
					while ($posts->have_posts()) : $posts->the_post();

						if ( $this->thumbs == 1 ) : // add thumbnail
							$content .= '<li class="widget-thumb"><a href="'. get_permalink() .'" title="'. esc_attr(get_the_title() ? get_the_title() : get_the_ID()) .'">'. get_the_post_thumbnail(get_the_ID(), 'widget_post_thumb') .'</a>';
						else:
							$content .= '<li>';
						endif;

						$content .= '<a href="'. get_permalink() .'" title="'. esc_attr(get_the_title() ? get_the_title() : get_the_ID()) .'">';
						if ( get_the_title() ) $content .= get_the_title(); else $content .= get_the_ID();
						$content .= '</a>';

						if ( $this->thumbs == 1 ) : // add date
							$content .= '<div class="widget-postmeta"><span class="widget-date">'. get_the_time(get_option('date_format')).'</span></div>';
						endif;

						$content .= '</li>';
					endwhile;
					$content .= '</ul>';
				endif;
			break;

			case 7: // Recent Comments
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

			case 8: // Recent Posts
				$posts = new WP_Query( apply_filters( 'msw_widget_tabbed_recent_posts_args', array( 'posts_per_page' => $this->number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
				if ($posts->have_posts()) :
					$content = '<ul>';
					while ($posts->have_posts()) : $posts->the_post();

						if ( $this->thumbs == 1 ) : // add thumbnail
							$content .= '<li class="widget-thumb"><a href="'. get_permalink() .'" title="'. esc_attr(get_the_title() ? get_the_title() : get_the_ID()) .'">'. get_the_post_thumbnail(get_the_ID(), 'widget_post_thumb') .'</a>';
						else:
							$content .= '<li>';
						endif;

						$content .= '<a href="'. get_permalink() .'" title="'. esc_attr(get_the_title() ? get_the_title() : get_the_ID()) .'">';
						if ( get_the_title() ) $content .= get_the_title(); else $content .= get_the_ID();
						$content .= '</a>';

						if ( $this->thumbs == 1 ) : // add date
							$content .= '<div class="widget-postmeta"><span class="widget-date">'. get_the_time(get_option('date_format')).'</span></div>';
						endif;

						$content .= '</li>';
					endwhile;
					$content .= '</ul>';
				endif;
			break;

			case 9: // Tag Cloud
				$content = '<div class="tagcloud">';
				$content .= wp_tag_cloud( apply_filters('msw_widget_tabbed_tagcloud_args', array('taxonomy' => 'post_tag', 'echo' => false) ) );
				$content .= "</div>\n";
			break;

			default:
				$content = "Please select the Tab Content in the Widget Settings.";
			break;
		}
		return $content;
	}

	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['number'] = (int)$new_instance['number'];
		$instance['thumbnails'] = isset($new_instance['thumbnails']);
		
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
					<?php _e('Tab 1:', 'magazine-sidebar-widgets') ?>
				</label>
				<select id="<?php echo $this->get_field_id('tab_content-'.$i); ?>" name="<?php echo $this->get_field_name('tab_content-'.$i); ?>">
					<option value="0" <?php selected($tab_content[$i], 0); ?>></option>
					<option value="1" <?php selected($tab_content[$i], 1); ?>><?php _e('Archives', 'magazine-sidebar-widgets'); ?></option>
					<option value="2" <?php selected($tab_content[$i], 2); ?>><?php _e('Categories', 'magazine-sidebar-widgets'); ?></option>
					<option value="3" <?php selected($tab_content[$i], 3); ?>><?php _e('Meta', 'magazine-sidebar-widgets'); ?></option>
					<option value="4" <?php selected($tab_content[$i], 4); ?>><?php _e('Pages', 'magazine-sidebar-widgets'); ?></option>
					<option value="5" <?php selected($tab_content[$i], 5); ?>><?php _e('Popular Posts', 'magazine-sidebar-widgets'); ?></option>
					<option value="6" <?php selected($tab_content[$i], 6); ?>><?php _e('Recent Comments', 'magazine-sidebar-widgets'); ?></option>
					<option value="7" <?php selected($tab_content[$i], 7); ?>><?php _e('Recent Posts', 'magazine-sidebar-widgets'); ?></option>
					<option value="8" <?php selected($tab_content[$i], 8); ?>><?php _e('Tag Cloud', 'magazine-sidebar-widgets'); ?></option>
				</select>
				
				<label for="<?php echo $this->get_field_id('tab_titles-'.$i); ?>"><?php _e('Title:', 'magazine-sidebar-widgets'); ?>
					<input id="<?php echo $this->get_field_id('tab_titles-'.$i); ?>" name="<?php echo $this->get_field_name('tab_titles-'.$i); ?>" type="text" value="<?php echo $tab_titles[$i]; ?>" />
				</label>
			</p>
						
		<?php endfor; ?>
				
		</div>	

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
<?php
	}
}

?>