<?php

if(!class_exists('Ctwg_Widget_Subscribers')){
	class Ctwg_Widget_Subscribers extends WP_Widget{
		
		function Ctwg_Widget_Subscribers(){
			$widget_ops = array('classname' => 'ctwg_subscriber_count', 'description' => __('Displays the subscriber count of your social networks.', 'cpocore'));
			$this->WP_Widget('cpotheme-subscriber-count', __('CPO - Subscriber Count', 'cpocore'), $widget_ops);
			$this->alt_option_name = 'ctwg_subscriber_count';
		}

		function widget($args, $instance){
			extract($args);
			$widget_id = str_replace('-', '_', $widget_id);
			$title = apply_filters('widget_title', $instance['title']);
			$facebook_username = strip_tags($instance['facebook_username']);
			
			echo $before_widget;
			if($title != '') echo $before_title.$title.$after_title; ?>
					
			<div class="subscriber_count" id="subscriber-count-<?php echo $widget_id; ?>">
				
				<?php if($facebook_username != ''): 
				$facebook_data = json_decode(file_get_contents('http://graph.facebook.com/'.$facebook_username), true); ?>
				<div class="subscriber_item" id="subscribers-facebook-<?php echo $widget_id; ?>" href="http://facebook.com/<?php echo $facebook_username; ?>">
					<div class="icon icon-facebook-sign"></div>
					<a class="value" href="http://facebook.com/<?php echo $facebook_username; ?>"><?php echo $facebook_data['likes']; ?></a>
					<span class="description"><?php _e('Fans', 'cpocore'); ?></span>
				</div>
				<?php endif; ?>
				<div class="clear"></div>
			</div>
			<?php echo $after_widget;
		}

		function update($new_instance, $old_instance){
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['facebook_username'] = strip_tags($new_instance['facebook_username']);
			return $instance;
		}

		function form($instance){
			$instance = wp_parse_args((array) $instance, array('title' => ''));
			$title = esc_attr($instance['title']);
			$facebook_username = esc_attr($instance['facebook_username']);?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'cpocore'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('facebook_username'); ?>"><?php _e('Facebook Page Title or ID', 'cpocore'); ?></label><br/>
				<input id="<?php echo $this->get_field_id('facebook_username'); ?>" name="<?php echo $this->get_field_name('facebook_username'); ?>" type="text" value="<?php echo $facebook_username; ?>" />
			</p>
		<?php }
	}
	add_action('widgets_init', 'ctwg_widget_subscribers');
	function ctwg_widget_subscribers() {
		register_widget('Ctwg_Widget_Subscribers');
	}
}