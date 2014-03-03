<?php

if(!class_exists('Ctwg_Widget_Portfolio')){
	class Ctwg_Widget_Portfolio extends WP_Widget{
		
		function Ctwg_Widget_Portfolio(){
			$widget_ops = array('classname' => 'ctwg_portfolio_items', 'description' => __('Displays elements from the portfolio.', 'cpocore'));
			$this->WP_Widget('cpotheme-portfolio-items', __('CPO - Portfolio Items', 'cpocore'), $widget_ops);
			$this->alt_option_name = 'ctwg_portfolio_items';
			add_action('save_post', array(&$this, 'flush_widget_cache'));
			add_action('deleted_post', array(&$this, 'flush_widget_cache'));
			add_action('switch_theme', array(&$this, 'flush_widget_cache'));
		}

		function widget($args, $instance){
			$cache = wp_cache_get('ctwg_portfolio_items', 'widget');
			if(!is_array($cache)) $cache = array();
			
			if(isset($cache[$args['widget_id']])){
				echo $cache[$args['widget_id']];
				return;
			}
			ob_start();
			extract($args);
			$title = apply_filters('widget_title', $instance['title']);
			$number = $instance['number'];
			$ordering = $instance['ordering'];
			if(!is_numeric($number)) $number = 5; elseif($number < 1) $number = 1; elseif($number > 99) $number = 99;
			if($ordering == 'date') $direction = 'DESC'; else $direction = 'ASC';
			
			$recent_posts = new WP_Query(array('post_type' => 'cpo_portfolio', 'posts_per_page' => $number, 'orderby' => $ordering, 'order' => $direction));
			if($recent_posts->have_posts()):
			echo $before_widget;
			if($title != '') echo $before_title.$title.$after_title; ?>
			
			<div class="portfolio_items" id="portfolio-item-<?php echo $widget_id; ?>">
				<?php while($recent_posts->have_posts()): $recent_posts->the_post(); ?>
				<div class="item">
					<a class="thumbnail" href="<?php the_permalink(); ?>" >
						<?php the_post_thumbnail('thumbnail', array('title' => '')); ?>
					</a>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					<div class="meta"><?php echo get_the_term_list(get_the_ID(), 'cpo_tax_portfolio', '', ', ', ''); ?></div>
				</div>
				<?php endwhile; ?>
			</div>
			<?php echo $after_widget;
			wp_reset_postdata();
			endif;
			$cache[$args['widget_id']] = ob_get_flush();
			wp_cache_add('ctwg_portfolio_items', $cache, 'widget');
		}

		function update($new_instance, $old_instance){
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['number'] = (int)$new_instance['number'];
			$instance['ordering'] = strip_tags($new_instance['ordering']);
			$this->flush_widget_cache();
			$alloptions = wp_cache_get('alloptions', 'options');
			if(isset($alloptions['ctwg_portfolio_items']))
			delete_option('ctwg_portfolio_items');
			return $instance;
		}

		function flush_widget_cache(){
			wp_cache_delete('ctwg_portfolio_items', 'widget');
		}

		function form($instance){
			$instance = wp_parse_args((array) $instance, array('title' => '', 'ordering' => 'menu_order'));
			$title = esc_attr($instance['title']);
			$ordering = esc_attr($instance['ordering']);
			if(!isset($instance['number']) || !$number = (int)$instance['number']) $number = 5; ?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'cpocore'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of Items', 'cpocore'); ?></label><br/>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>
			<p>
				<select id="<?php echo $this->get_field_id('ordering'); ?>" name="<?php echo $this->get_field_name('ordering'); ?>">
				<label for="<?php echo $this->get_field_id('ordering'); ?>"><?php _e('Ordering', 'cpocore'); ?></label><br/>
					<option value="menu_order" <?php if($ordering == 'menu_order') echo 'selected="selected"'; ?>><?php _e('By Order Field', 'cpocore'); ?></option>
					<option value="date" <?php if($ordering == 'date') echo 'selected="selected"'; ?>><?php _e('By Date', 'cpocore'); ?></option>
					<option value="title" <?php if($ordering == 'title') echo 'selected="selected"'; ?>><?php _e('By Title', 'cpocore'); ?></option>
				</select>
			</p>
		<?php }
	}
	add_action('widgets_init', 'ctwg_widget_portfolio');
	function ctwg_widget_portfolio() {
		register_widget('Ctwg_Widget_Portfolio');
	}
}
