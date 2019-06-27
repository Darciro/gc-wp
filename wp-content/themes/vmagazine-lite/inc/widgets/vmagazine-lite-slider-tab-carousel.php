<?php
/**
 * Vmagazine: Category Posts (Slider)
 *
 * Widget to display selected category posts as on slider style.
 *
 * @package AccessPress Themes
 * @subpackage Vmagazine-lite
 * @since 1.0.0
 */

add_action( 'widgets_init', 'vmagazine_lite_register_category_slider_tab_carousel_widget' );

function vmagazine_lite_register_category_slider_tab_carousel_widget() {
    register_widget( 'vmagazine_lite_category_slider_tab_carousel' );
}

class vmagazine_lite_category_slider_tab_carousel extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_ops = array( 
            'classname' => 'vmagazine_lite_category_slider_tab_carousel',
            'description' => esc_html__( 'Display posts from selected category as slider in Tab.', 'vmagazine-lite' )
        );
        $width = array(
                'width'  => 600
        );
        parent::__construct( 'vmagazine_lite_category_slider_tab_carousel', esc_html__( 'Vmagazine-Lite : Slider Tab Carousel', 'vmagazine-lite' ), $widget_ops, $width );
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
                'vmagazine_widgets_layout_img'   => VMAG_WIDGET_IMG_URI.'stc.png',
                'vmagazine_widgets_field_type'   => 'widget_layout_image'
            ),

            'block_title' => array(
                'vmagazine_widgets_name'         => 'block_title',
                'vmagazine_widgets_title'        => esc_html__( 'Block Title', 'vmagazine-lite' ),
                'vmagazine_widgets_field_type'   => 'text'
            ),

            'block_post_type' => array(
                    'vmagazine_widgets_name'        => 'block_post_type',
                    'vmagazine_widgets_title'       => esc_html__( 'Block Display Type', 'vmagazine-lite' ),
                    'vmagazine_widgets_field_type'  => 'radio',
                    'vmagazine_widgets_default'     => 'latest_posts',
                    'vmagazine_widgets_field_options' => $vmagazine_posts_type
                ),

            'block_single_cat' => array(
                'vmagazine_widgets_name' => 'block_single_cat',
                'vmagazine_widgets_title' => esc_html__( 'Choose categories', 'vmagazine-lite' ),
                'vmagazine_widgets_default'     => 0,
                'vmagazine_widgets_field_type' => 'select',
                'vmagazine_widgets_field_options' => $vmagazine_cat_dropdown
            ), 

            'block_posts_count' => array(
                'vmagazine_widgets_name'         => 'block_posts_count',
                'vmagazine_widgets_title'        => esc_html__( 'No. of Posts', 'vmagazine-lite' ),
                'vmagazine_widgets_default'      => 4,
                'vmagazine_widgets_field_type'   => 'number'
            ),
                      
            //design

            'block_section_meta' => array(
                'vmagazine_widgets_name' => 'block_section_meta',
                'vmagazine_widgets_title' => esc_html__( 'Show/Hide Meta', 'vmagazine-lite' ),
                'vmagazine_widgets_default'=>'show',
                'vmagazine_widgets_field_options'=>array('show'=>'Show','hide'=>'Hide'),
                'vmagazine_widgets_description'  => esc_html__('Show or hide post meta options like author name, post date etc','vmagazine-lite'),
                'vmagazine_widgets_field_type' => 'switch'

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
    public function widget( $args, $instance ) {
        extract( $args );
        if( empty( $instance ) ) {
            return ;
        }

        $vmagazine_block_title       = empty( $instance['block_title'] ) ? '' : $instance['block_title'];
        $vmagazine_block_posts_count = empty( $instance['block_posts_count'] ) ? 6 : $instance['block_posts_count'];
        $vmagazine_block_posts_type = empty($instance['block_post_type']) ? 'latest_posts' : $instance['block_post_type'];
        $vmagazine_block_cat_id = empty($instance['block_single_cat']) ? null: $instance['block_single_cat'];
        $block_section_meta = isset( $instance['block_section_meta'] ) ? $instance['block_section_meta'] : 'show';
       
        echo wp_kses_post($before_widget);
    ?>
        <div class="vmagazine-slider-tab-carousel">
        <div class="block-post-wrapper">
            <div class="block-header clearfix">
                <?php 
                   vmagazine_lite_widget_title( $vmagazine_block_title, $title_url=null, $cat_id=null );
                ?>
            </div><!-- .block-header-->  
            <div class="block-content-wrapper-carousel">
                <div class="block-cat-content-carousel">
            
                <?php 
                    $block_args = vmagazine_lite_query_args( $vmagazine_block_posts_type, $vmagazine_block_posts_count, $vmagazine_block_cat_id );
                    $block_query = new WP_Query( $block_args );
                    if( $block_query->have_posts() ) {
                        echo '<div class="tab-cat-slider-carousel">';
                        while( $block_query->have_posts() ) {
                            $block_query->the_post();
                            $image_id   = get_post_thumbnail_id();
                            $img_src    = vmagazine_lite_home_element_img('vmagazine-post-slider-lg');
                            $image_alt  = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
                            ?>
                            <div class="single-post">
                                <div class="post-thumb">
                                    <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($image_alt); ?>" title="<?php the_title(); ?>" />
                                    <div class="image-overlay"></div>
                                    <?php do_action( 'vmagazine_post_format_icon' ); ?>
                                </div>
                                <div class="post-caption">
                                     <?php if( $block_section_meta == 'show' ): ?>
                                        <div class="post-meta clearfix">
                                            <?php do_action( 'vmagazine_icon_meta' ); ?>
                                        </div>
                                     <?php endif; ?>  
                                    <h3 class="large-font">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php echo vmagazine_lite_title_excerpt(45);// WPCS: XSS OK. ?>
                                        </a>
                                    </h3>
                                </div><!-- .post-caption -->
                            </div><!-- .single-post -->
                            <?php
                        }
                        echo '</div>';
                    }
                wp_reset_query();
                ?>
                </div>
            </div><!-- block-content-wraper-->
        </div><!-- .block-post-wrapper -->
    </div>
    <?php
        echo wp_kses_post($after_widget);
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param   array   $new_instance   Values just sent to be saved.
     * @param   array   $old_instance   Previously saved values from database.
     *
     * @uses   vmagazine_lite_widgets_updated_field_value()      defined in vmagazine-widget-fields.php
     *
     * @return  array Updated safe values to be saved.
     */
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
     * @param   array $instance Previously saved values from database.
     *
     * @uses   vmagazine_lite_widgets_show_widget_field()        defined in vmagazine-widget-fields.php
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