<?php
/**
 * Vmagazine: Featured Slider
 *
 * Widget to be manage slider section with featured posts which have options to hide featured posts.
 *
 * @package AccessPress Themes
 * @subpackage Vmagazine
 * @since 1.0.0
 */
add_action( 'widgets_init', 'vmagazine_lite_register_featured_slider_widget' );

function vmagazine_lite_register_featured_slider_widget() {
    register_widget( 'vmagazine_lite_featured_slider' );
}

class vmagazine_lite_Featured_Slider extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        
        $width = array(
                'width'  => 600
        );
        parent::__construct(
                'vmagazine_lite_featured_slider', esc_html__( 'Vmagazine-Lite : Featured Slider', 'vmagazine-lite' ), array(
                'description' => esc_html__( 'Widget to control slider and featured posts section.', 'vmagazine-lite' )   
                ),$width 
        );
    }

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */
    private function widget_fields() {
        global $vmagazine_cat_dropdown, $vmagazine_posts_type,$vmagazine_cat_slider_layout;
        $fields = array(
             //widget wrapper div start
        
                'block_layout' => array(
                    'vmagazine_widgets_name'         => 'block_layout',
                    'vmagazine_widgets_title'        => esc_html__( 'Layout will be like this', 'vmagazine-lite' ),
                    'vmagazine_widgets_layout_img'   => VMAG_WIDGET_IMG_URI.'fs.png',
                    'vmagazine_widgets_field_type'   => 'widget_layout_image'
                ),

                     'block_title' => array(
                        'vmagazine_widgets_name'         => 'block_title',
                        'vmagazine_widgets_title'        => esc_html__( 'Block Title', 'vmagazine-lite' ),
                        'vmagazine_widgets_field_type'   => 'text'
                    ),


                    'slider_section_header' => array(
                        'vmagazine_widgets_name' => 'slider_section_header',
                        'vmagazine_widgets_title' => esc_html__( 'Slider Section', 'vmagazine-lite' ),
                        'vmagazine_widgets_field_type' => 'section_header'
                    ),
                    'vmagazine_slider_post_type' => array(
                        'vmagazine_widgets_name' => 'vmagazine_slider_post_type',
                        'vmagazine_widgets_title' => esc_html__( 'Slider Display Type', 'vmagazine-lite' ),
                        'vmagazine_widgets_field_type' => 'radio',
                        'vmagazine_widgets_default' => 'latest_posts',
                        'vmagazine_widgets_field_options' => $vmagazine_posts_type
                    ),
                    'vmagazine_slider_category' => array(
                        'vmagazine_widgets_name' => 'vmagazine_slider_category',
                        'vmagazine_widgets_title' => esc_html__( 'Category for Slider', 'vmagazine-lite' ),
                        'vmagazine_widgets_default'      => 0,
                        'vmagazine_widgets_field_type' => 'select',
                        'vmagazine_widgets_field_options' => $vmagazine_cat_dropdown
                    ),
                    'vmagazine_slide_count' => array(
                        'vmagazine_widgets_name' => 'vmagazine_slide_count',
                        'vmagazine_widgets_title' => esc_html__( 'No of slides', 'vmagazine-lite' ),
                        'vmagazine_widgets_default' => 5,
                        'vmagazine_widgets_field_type' => 'number'
                    ),



                    //Feature posts

                    'featured_section_header' => array(
                        'vmagazine_widgets_name' => 'featured_section_header',
                        'vmagazine_widgets_title' => esc_html__( 'Featured Posts Section', 'vmagazine-lite' ),
                        'vmagazine_widgets_field_type' => 'section_header'
                    ),

                    'featured_section_option' => array(
                        'vmagazine_widgets_name' => 'featured_section_option',
                        'vmagazine_widgets_title' => esc_html__( 'Show/Hide Featured Section', 'vmagazine-lite' ),
                        'vmagazine_widgets_default'=>'show',
                        'vmagazine_widgets_field_options'=>array('show'=>'Show','hide'=>'Hide'),
                        'vmagazine_widgets_field_type' => 'switch',
                        'vmagazine_widgets_description'  => esc_html__('Show or hide featured section below slider','vmagazine-lite'),
                    ),
                    'vmagazine_featured_post_type' => array(
                        'vmagazine_widgets_name' => 'vmagazine_featured_post_type',
                        'vmagazine_widgets_title' => esc_html__( 'Featured Posts Display', 'vmagazine-lite' ),
                        'vmagazine_widgets_field_type' => 'radio',
                        'vmagazine_widgets_default' => 'latest_posts',
                        'vmagazine_widgets_field_options' => $vmagazine_posts_type
                    ),
                    'vmagazine_featured_category' => array(
                        'vmagazine_widgets_name' => 'vmagazine_featured_category',
                        'vmagazine_widgets_title' => esc_html__( 'Category for Featured Posts', 'vmagazine-lite' ),
                        'vmagazine_widgets_default'      => 0,
                        'vmagazine_widgets_field_type' => 'select',
                        'vmagazine_widgets_field_options' => $vmagazine_cat_dropdown
                    ),
                    'vmagazine_feature_count' => array(
                        'vmagazine_widgets_name' => 'vmagazine_feature_count',
                        'vmagazine_widgets_title' => esc_html__( 'No of posts', 'vmagazine-lite' ),
                        'vmagazine_widgets_default' => 5,
                        'vmagazine_widgets_field_type' => 'number'
                    ),
                    'block_section_excerpt' => array(
                        'vmagazine_widgets_name' => 'block_section_excerpt',
                        'vmagazine_widgets_title' => esc_html__( 'Excerpt for post description', 'vmagazine-lite' ),
                        'vmagazine_widgets_description' => esc_html__( 'Enter excerpts in number of letters Default: 150', 'vmagazine-lite' ),
                        'vmagazine_widgets_field_type' => 'number',
                        'vmagazine_widgets_default'     => '150'
                    ),
                     'vmagazine_featured_view_all_text' => array(
                        'vmagazine_widgets_name'         => 'vmagazine_featured_view_all_text',
                        'vmagazine_widgets_title'        => esc_html__( 'View All Button Text', 'vmagazine-lite' ),
                        'vmagazine_widgets_field_type'   => 'text'
                    ),

                    'block_section_meta' => array(
                        'vmagazine_widgets_name' => 'block_section_meta',
                        'vmagazine_widgets_title' => esc_html__( 'Hide/Show Meta', 'vmagazine-lite' ),
                        'vmagazine_widgets_default'=>'show',
                        'vmagazine_widgets_field_options'=>array('show'=>'Show','hide'=>'Hide'),
                        'vmagazine_widgets_field_type' => 'switch',
                        'vmagazine_widgets_description'  => esc_html__('Show or hide post meta options like author name, post date etc','vmagazine-lite'),
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
        $vmagazine_block_title = empty($instance['block_title']) ? '' : $instance['block_title'];
        $vmagazine_slide_count = empty($instance['vmagazine_slide_count']) ? 5 : $instance['vmagazine_slide_count'];
        $vmagazine_slider_type = empty($instance['vmagazine_slider_post_type']) ? 'latest_posts' : $instance['vmagazine_slider_post_type'];
        $vmagazine_slider_cat_id = empty($instance['vmagazine_slider_category']) ? 0 : $instance['vmagazine_slider_category'];
        $featured_option = empty($instance['featured_section_option']) ? 'show' : $instance['featured_section_option'];
        $vmagazine_feature_count = empty($instance['vmagazine_feature_count']) ? 5 : $instance['vmagazine_feature_count'];
        $featured_type = empty($instance['vmagazine_featured_post_type']) ? 'latest_posts' : $instance['vmagazine_featured_post_type'];
        $featured_cat_id = empty($instance['vmagazine_featured_category']) ? 0 : $instance['vmagazine_featured_category'];
        $vmagazine_featured_view_all_text = empty($instance['vmagazine_featured_view_all_text']) ? '' : $instance['vmagazine_featured_view_all_text'];
        $block_section_excerpt = empty($instance['block_section_excerpt']) ? '' : $instance['block_section_excerpt'];
        $block_section_meta = empty($instance['block_section_meta']) ? 'show' : $instance['block_section_meta'];

        echo wp_kses_post($before_widget);
    ?>

    <div class="vmagazine-featured-slider featured-slider-wrapper">
        <h4 class="block-title">
            <span class="title-bg"><?php echo esc_attr($vmagazine_block_title); ?></span>
        </h4>
        
        <div class="section-wrapper clearfix">
            <div class="slider-section <?php if( $featured_option == 'show' ) { echo 'slider-fullwidth'; }?>">                    
                <?php 
                    $vmagazine_slider_args = vmagazine_lite_query_args( $vmagazine_slider_type, $vmagazine_slide_count, $vmagazine_slider_cat_id  );
                    $vmagazine_slider_query = new WP_Query( $vmagazine_slider_args );
                    if( $vmagazine_slider_query->have_posts() ) {
                        echo '<ul class="featuredSlider cS-hidden">';
                        while( $vmagazine_slider_query->have_posts() ) {
                            $vmagazine_slider_query->the_post();
                            $image_id = get_post_thumbnail_id();
                            $img_src = vmagazine_lite_home_element_img('vmagazine-post-slider-lg');
                           
                            $image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
                            if( has_post_thumbnail() ) {
                                ?>
                                <li class="slide">
                                    <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" title="<?php the_title(); ?>">
                                    <div class="slider-caption">
                                        <?php do_action( 'vmagazine_post_cat_or_tag_lists' );

                                        if( $block_section_meta == 'show' ): ?>
                                        <div class="post-meta">
                                             <?php do_action( 'vmagazine_icon_meta' ); ?>
                                        </div>
                                        <?php endif; ?>

                                        <h3 class="featured large-font">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_title(); ?>
                                            </a>
                                        </h3>
                                        
                                    </div>
                                    
                                </li>
                                <?php
                            }
                        }
                        echo '</ul>';
                    }
                    wp_reset_postdata();
                ?>                      
            </div><!-- .slider-section -->
            <?php if( $featured_option == 'show' ) { ?>
            <div class="featured-posts">
                <?php 
                    $vmagazine_featured_args = vmagazine_lite_query_args( $featured_type, $vmagazine_feature_count, $featured_cat_id  );
                    $vmagazine_featured_query = new WP_Query( $vmagazine_featured_args );
                    if( $vmagazine_featured_query->have_posts() ) {
                        echo '<ul class="featuredpost">';
                        while( $vmagazine_featured_query->have_posts() ) {
                            $vmagazine_featured_query->the_post();
                            $image_id = get_post_thumbnail_id();
                            
                            $img_src = vmagazine_lite_home_element_img('vmagazine-slider-thumb');
                            $image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
                            if( has_post_thumbnail() ) {
                                ?>
                                <li class="f-slide">
                                    <a class="f-slider-img" href="<?php the_permalink(); ?>">
                                        <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($image_alt); ?>" title="<?php the_title(); ?>">
                                    </a>                                            
                                    <div class="slider-caption">
                                        <?php if( $block_section_meta == 'show' ): ?>
                                        <div class="post-meta">
                                            <?php do_action( 'vmagazine_lite_icon_meta' ); ?>
                                        </div>
                                       <?php endif; ?>
                                       
                                        <h3 class="small-font">
                                            <a href="<?php the_permalink(); ?>">
                                                 <?php the_title(); ?>
                                            </a>
                                        </h3>
                                        <?php if($block_section_excerpt): ?>
                                            <div class="post-content">
                                                <?php echo vmagazine_lite_get_excerpt_content( absint($block_section_excerpt) ) // WPCS: XSS OK.?> 
                                            </div>
                                        <?php endif; ?>
                                        
                                    </div>
                                    
                                </li>
                                <?php
                            }
                        }
                        echo '</ul>';
                    }
                    if( $vmagazine_featured_view_all_text ){
                        vmagazine_lite_block_view_all( $featured_cat_id, $vmagazine_featured_view_all_text );    
                    }
                    
                    wp_reset_postdata();
                ?>              
            </div><!-- .featured-posts -->
            <?php }?>
        </div><!-- .section-wrapper -->
    </div><!-- .featured-slider-wrapper -->
      

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
     * @uses    vmagazine_widgets_updated_field_value()     defined in vmagazine-widget-fields.php
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
     * @uses    vmagazine_widgets_show_widget_field()       defined in widget-fields.php
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