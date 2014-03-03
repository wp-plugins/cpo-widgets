<?php

if(!class_exists('Ctwg_Widget_RecentItems')){
	class Ctwg_Widget_RecentItems extends WP_Widget{
		
		function Ctwg_Widget_RecentItems(){
			$widget_ops = array('classname' => 'ctwg_recent_items', 'description' => __('Displays the most recent items of your choice.', 'cpocore'));
			$this->WP_Widget('cpotheme-recent-posts', __('CPO - Recent Items', 'cpocore'), $widget_ops);
			$this->alt_option_name = 'ctwg_recent_items';
			add_action('save_post', array(&$this, 'flush_widget_cache'));
			add_action('deleted_post', array(&$this, 'flush_widget_cache'));
			add_action('switch_theme', array(&$this, 'flush_widget_cache'));
		}

		function widget($args, $instance){
			$cache = wp_cache_get('ctwg_recent_items', 'widget');
			if(!is_array($cache)) $cache = array();
			
			if(isset($cache[$args['widget_id']])){
				echo $cache[$args['widget_id']];
				return;
			}
			ob_start();
			extract($args);
			$title = apply_filters('widget_title', $instance['title']);
			$number = $instance['number'];
			$type = $instance['type'];
			if(!is_numeric($number)) $number = 5; elseif($number < 1) $number = 1; elseif($number > 99) $number = 99;
			
			$recent_items = new WP_Query(array('post_type' => $type, 'posts_per_page' => $number, 'ignore_sticky_posts' => 1));
			if($recent_items->have_posts()):
			echo $before_widget;
			if($title != '') echo $before_title.$title.$after_title; ?>
			
			<div class="widget_content">
				<?php while($recent_items->have_posts()): $recent_items->the_post(); ?>
				<div class="item">
					<a class="thumbnail" href="<?php the_permalink(); ?>" >
						<?php the_post_thumbnail('thumbnail', array('title' => '')); ?>
					</a>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					<div class="meta"><?php the_time(get_option('date_format')); ?></div>
				</div>
				<?php endwhile; ?>
			</div>
			<?php echo $after_widget;
			wp_reset_postdata();
			endif;
			$cache[$args['widget_id']] = ob_get_flush();
			wp_cache_add('ctwg_recent_items', $cache, 'widget');
		}

		function update($new_instance, $old_instance){
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['number'] = (int) $new_instance['number'];
			$this->flush_widget_cache();
			$alloptions = wp_cache_get('alloptions', 'options');
			if(isset($alloptions['ctwg_recent_entries']))
			delete_option('ctwg_recent_entries');
			return $instance;
		}

		function flush_widget_cache(){
			wp_cache_delete('ctwg_recent_items', 'widget');
		}

		function form($instance){
			$instance = wp_parse_args((array) $instance, array('title' => ''));
			$title = esc_attr($instance['title']);
			if(!isset($instance['number']) || !$number = (int)$instance['number']) $number = 5; ?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'cpocore'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Type of Post', 'cpocore'); ?></label><br/>
				<input class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" type="text" value="<?php echo $type; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of Posts', 'cpocore'); ?></label><br/>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>
		<?php }
	}
	register_widget('Ctwg_Widget_RecentItems');
}