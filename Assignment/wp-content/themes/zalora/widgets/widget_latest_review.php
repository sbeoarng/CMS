<?php
/**
 * Plugin Name: shadowfiend: Latest Review Widget
 * Plugin URI: http://shadowfiend-design.info/
 * Description: This widget displays Latest Review posts.
 * Version: 1.0
 * Author: shadowfiend
 * Author URI: http://shadowfiend-design.info/
 *
 */


/**
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'shadowfiend_register_latest_reviews_widget' );

function shadowfiend_register_latest_reviews_widget() {
	register_widget( 'shadowfiend_latest_review' );
}

/**
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 */
class shadowfiend_latest_review extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget-posts-list', 'description' => esc_html__('[Sidebar widget] Displays recent review posts in sidebar.', 'zalora') );

		/* Create the widget. */
		parent::__construct( 'shadowfiend_latest_review', esc_html__('*shadowfiend: Widget Latest Reviews', 'zalora'), $widget_ops);
	}


	/**
	 *display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
        $title = apply_filters('widget_title', $instance['title'] );
		$entries_display = esc_attr($instance['entries_display']);
        
        $cat_id = $instance['category'];
        $style = $instance['style'];
        if(isset($style) && (($style == 'style-1') || ($style == ''))) {
            $style = 'type-in';
        }
		if( (!isset($entries_display)) || ($entries_display == NULL)){ 
            $entries_display = '5'; 
        }
        $meta_ar = array('cat', 'date');
		echo $before_widget;
?>      
    <?php
        if ($cat_id[0] == 'feat') {    
            $args = array(
				'post__in'  => get_option( 'sticky_posts' ),
				'post_status' => 'publish',
				'ignore_sticky_posts' => 1,
                'orderby' => 'date',
                'order'=> 'DESC',
				'posts_per_page' => $entries_display,
                'meta_query' => array(
        				array(
        					'key' => 'shadowfiend_review_checkbox',
        					'value' => '1',
        				)
                    )
                );  
        } else if ($cat_id[0] == 'all'){ 
      		    $args = array(
    				'post_status' => 'publish',
    				'ignore_sticky_posts' => 1,
                    'orderby' => 'date',
                    'order'=> 'DESC',
    				'posts_per_page' => $entries_display,
                    'meta_query' => array(
        				array(
        					'key' => 'shadowfiend_review_checkbox',
        					'value' => '1',
        				)
                     )
                );
        } else {
		$args = array(
				'category__in' => $cat_id,
				'post_status' => 'publish',
                'orderby' => 'date',
                'order'=> 'DESC',
				'ignore_sticky_posts' => 1,
				'posts_per_page' => $entries_display,
                'meta_query' => array(
        				array(
        					'key' => 'shadowfiend_review_checkbox',
        					'value' => '1',
        				)
                    )
                );
        }
        $review_posts = new WP_Query( $args );
        
		if ( $title ) {
            echo $before_title .esc_html($title). $after_title;
        }
        if ( $review_posts -> have_posts() ) : ?>
        <div class="post-list-wrap">
			<ul class="small-posts">
                <?php $query = new WP_Query( $args ); ?>
				<?php while($query->have_posts()): $query->the_post(); $post_id = get_the_ID(); ?>		
                    <li class="post-item <?php echo esc_attr($style);?> clear-fix">
                        <div class="thumb hide-thumb">	
                            <?php 
                                if(isset($style) && ($style == 'style-3')) {
                                    echo (zalora_get_thumbnail($post_id, 'zalora_200_200'));
                            	}else {
                                    echo (zalora_get_thumbnail($post_id, 'zalora_200_200'));
                            	}
                            ?>							
                      		<?php 
                                echo zalora_review_score($post_id);
                            ?> 
                        </div>						
						<div class="post-info">								
							<h4 class="post-title">
								<a href="<?php the_permalink() ?>">
									<?php 
										$title = get_the_title();
										echo zalora_the_excerpt_limit($title, 12);
									?>
								</a>
							</h4>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
		</div>		
        <?php endif;?>
		<?php echo $after_widget;
	}
	
	/**
	 * update widget settings
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
        $instance['title'] = $new_instance['title'];
		$instance['entries_display'] = strip_tags($new_instance['entries_display']);
        $instance['category'] = $new_instance['category'];
        $instance['style'] = $new_instance['style'];
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {
        $widget_cat_id = $this->get_field_id( 'category' );
		$defaults = array('title' => 'Latest Reviews', 'entries_display' => 5, 'category' => 'all', 'style' => 1);
		$instance = wp_parse_args((array) $instance, $defaults);
	?>
    <p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><strong><?php esc_attr_e('Title:', 'zalora'); ?></strong></label>
		<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
	</p>
            
    <p><label for="<?php echo $this->get_field_id( 'entries_display' ); ?>"><strong><?php esc_attr_e('Number of entries to display: ', 'zalora'); ?></strong></label>
	<input type="text" id="<?php echo $this->get_field_id('entries_display'); ?>" name="<?php echo $this->get_field_name('entries_display'); ?>" value="<?php echo $instance['entries_display']; ?>" style="width:100%;" /></p>        
    <p>     
        <label for="<?php echo $this->get_field_id( 'style' ); ?>"><strong><?php   esc_attr_e('Style: ','zalora'); ?></strong></label>    		 	
        <select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">            
            <option value="style-1" <?php if ($instance['style'] == 'style-1') echo 'selected="selected"'; ?>><?php esc_attr_e('Style 1', 'zalora');?></option>               
            <option value="style-2" <?php if ($instance['style'] == 'style-2') echo 'selected="selected"'; ?>><?php esc_attr_e('Style 2', 'zalora');?></option> 
            <option value="style-3" <?php if ($instance['style'] == 'style-3') echo 'selected="selected"'; ?>><?php esc_attr_e('Style 3', 'zalora');?></option>                           	
         </select>          
    </p>
    <!-- Categories
    --------------------------------------------->
	<p>
		<label for="<?php echo $this->get_field_id('category'); ?>"><strong><?php esc_attr_e('Post Source:', 'zalora'); ?></strong></label> 
		<select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>[]" class="widefat categories tn-category-field" size="5" multiple='multiple' style="width:100%;">
			<option value='feat'><?php esc_attr_e( 'Featured Posts', 'zalora' ); ?></option>
            <option value='all'><?php esc_attr_e( 'All Categories', 'zalora' ); ?></option>
			<?php $categories = get_categories('hide_empty=0&depth=1&type=post'); ?>
			<?php foreach($categories as $category) { ?>
			<option value='<?php echo $category->term_id; ?>'><?php echo $category->cat_name; ?></option>
			<?php } ?>
		</select>
	</p>    	
    <script>
        jQuery(document).ready(function($){
                <?php
                    $cat_array = json_encode($instance['category']);
                    echo "var instant = ". $cat_array . ";\n";
                ?>
                var status = 0;
                var widget_cat_id = "<?php echo esc_attr($widget_cat_id); ?>";
                $("#"+widget_cat_id).find("option").each(function(){
                    $this = $(this);
                    if (($(instant).length == 0) && ($this.attr('value') == 'all')) {
                        $this.attr('selected','selected');
                        return false;
                    }
                    $(instant).each(function(index, value){
                        if(value == $this.attr('value')){
                            $this.attr('selected','selected');
                        }
                    });
                    if ((($this.attr('value') == 'feat') || ($this.attr('value') == 'all')) && ($this.is(':selected'))){
                        return false;
                    }
                });

        });
        </script>	
	<?php
	}
}
?>