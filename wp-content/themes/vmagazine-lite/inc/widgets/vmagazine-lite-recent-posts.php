<?php
/**
 *
 * @package Vmagazine-lite
 */
 if(!function_exists('vmagazine_lite_recent_post_widget')){
add_action('widgets_init', 'vmagazine_lite_recent_post_widget');

function vmagazine_lite_recent_post_widget() {
    register_widget('vmagazine_lite_recent_post');
}
}
if(!class_exists('vmagazine_lite_Recent_Post')){
class vmagazine_lite_Recent_Post extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
                'vmagazine_lite_recent_post', 'Vmagazine-Lite : Recent Posts', array(
            'description' => esc_html__('Recent Posts', 'vmagazine-lite')
                )
        );
    }

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */
    private function widget_fields() {
        global $vmagazine_cat_dropdown, $vmagazine_posts_type;
         
        $fields = array(

            'block_layout' => array(
                'vmagazine_widgets_name'         => 'block_layout',
                'vmagazine_widgets_title'        => esc_html__( 'Layout will be like this', 'vmagazine-lite' ),
                'vmagazine_widgets_layout_img'   => VMAG_WIDGET_IMG_URI.'rp.png',
                'vmagazine_widgets_field_type'   => 'widget_layout_image'
            ),


            'vmagazine_recent_post_title' => array(
                'vmagazine_widgets_name' => 'vmagazine_recent_post_title',
                'vmagazine_widgets_title' => esc_html__('Title', 'vmagazine-lite'),
                'vmagazine_widgets_field_type' => 'text',
            ),

            'block_post_type' => array(
                'vmagazine_widgets_name' => 'block_post_type',
                'vmagazine_widgets_title' => esc_html__( 'Block posts: ', 'vmagazine-lite' ),
                'vmagazine_widgets_field_type' => 'radio',
                'vmagazine_widgets_default' => 'latest_posts',
                'vmagazine_widgets_field_options' => $vmagazine_posts_type
            ),

            'block_post_category' => array(
                'vmagazine_widgets_name' => 'block_post_category',
                'vmagazine_widgets_title' => esc_html__( 'Category for Block Posts', 'vmagazine-lite' ),
                'vmagazine_widgets_default'      => 0,
                'vmagazine_widgets_field_type' => 'select',
                'vmagazine_widgets_field_options' => $vmagazine_cat_dropdown
            ),

            'vmagazine_recent_post_per_page' => array(
                'vmagazine_widgets_name' => 'vmagazine_recent_post_per_page',
                'vmagazine_widgets_title' => esc_html__('Posts Per Page', 'vmagazine-lite'),
                'vmagazine_widgets_default' => 3,
                'vmagazine_widgets_field_type' => 'number',
            ),
        );

        return $fields;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        extract($args);
        $vmagazine_recent_post_title = $instance['vmagazine_recent_post_title'];
        $block_posts_type    = empty( $instance['block_post_type'] ) ? 'latest_posts' : $instance['block_post_type'];
        $block_post_category    = empty( $instance['block_post_category'] ) ? null: $instance['block_post_category'];
        $vmagazine_recent_post_per_page = $instance['vmagazine_recent_post_per_page'];

        if($vmagazine_recent_post_per_page == ''){
            $vmagazine_recent_post_per_page = '-1';
        }

        echo wp_kses_post($before_widget);
            if($vmagazine_recent_post_title){
               vmagazine_lite_widget_title( $vmagazine_recent_post_title, $vmagazine_block_title_url=null, $cat_id=null );
                 $vmagazine_recent_post_args = vmagazine_lite_query_args( $block_posts_type, $vmagazine_recent_post_per_page, $block_post_category );
                
                $vmagazine_recent_post_query = new WP_Query($vmagazine_recent_post_args);
                if($vmagazine_recent_post_query->have_posts()):
                
                    ?>
                    <div class="vmagazine-rec-posts recent-post-widget block_layout_1">
                        <?php
                        while($vmagazine_recent_post_query->have_posts()):
                            $vmagazine_recent_post_query->the_post();
                            
                            $img_src = vmagazine_lite_home_element_img('vmagazine-small-square-thumb');    
                            if( $img_src || get_the_title() ){
                                ?>
                                    <div class="recent-posts-content wow fadeInUp">
                                        <div class="image-recent-post">
                                          <a href="<?php the_permalink(); ?>">
                                            <?php echo vmagazine_lite_load_images($img_src); // WPCS: XSS OK.?>
                                          </a>
                                        </div>
                                        <div class="recent-post-content">
                                            <?php 
                                                do_action('vmagazine_single_cat'); 
                                            ?>
                                            <a href="<?php the_permalink()?>">
                                                <?php the_title(); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php
                            }
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                    <?php
                endif;
            }
        echo wp_kses_post($after_widget);

        /**
        * Widget stylings
        */


    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $widget_fields = $this->widget_fields();

        // Loop through fields
        foreach ( $widget_fields as $widget_field ) {

            extract( $widget_field );

            // Use helper function to get updated field values
            $instance[$vmagazine_widgets_name] = vmagazine_lite_widgets_updated_field_value( $widget_field, $new_instance[$vmagazine_widgets_name] );
        }

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param	array $instance Previously saved values from database.
     *
     * @uses	vmagazine_widgets_show_widget_field()		defined in widget-fields.php
     */
  public function form( $instance ) {
        $widget_fields = $this->widget_fields();

        // Loop through fields
        foreach ( $widget_fields as $widget_field ) {

            // Make array elements available as variables
            extract( $widget_field );
            $vmagazine_widgets_field_value = !empty( $instance[$vmagazine_widgets_name]) ? esc_attr($instance[$vmagazine_widgets_name] ) : '';
           vmagazine_lite_widgets_show_widget_field( $this, $widget_field, $vmagazine_widgets_field_value );
        }
    }

}
}
