<?php
/**
 * Additional  Settings panel in customizer section
 *
 * @package AccessPress Themes
 * @subpackage Vmagazine-lite
 * @since 1.0.0
 */


add_action( 'customize_register', 'vmagazine_lite_additional_settings_panel_register' );

if( !function_exists( 'vmagazine_lite_additional_settings_panel_register' ) ):
	function vmagazine_lite_additional_settings_panel_register( $wp_customize ) { 

		/**
		 * Add General Settings panel
		 */
		$wp_customize->add_panel('vmagazine_additional_settings_panel', array(
	        		'priority'       => 35,
	            	'capability'     => 'edit_theme_options',
	            	'theme_supports' => '',
	            	'title'          => esc_html__( 'Additional Settings', 'vmagazine-lite' ),
	            ) 
	    );
/*------------------------------------------------------------------------------------*/
		/**
		 * Home Page
		 */
		$wp_customize->add_section('vmagazine_additional_home',array(
	            'title'		=> esc_html__( 'HomePage Settings', 'vmagazine-lite' ),
	            'panel'     => 'vmagazine_additional_settings_panel',
	            'priority'  => 5,
	        )
	    );

	    /**
	     * Wow animation at home
	     */
	    $wp_customize->add_setting( 'vmagazine_wow_animation_option', array(
	            'default' 			=> 'enable',
	            'sanitize_callback' => 'vmagazine_sanitize_switch_enable_option',
	            )
	    );
	    $wp_customize->add_control( new vmagazine_lite_Customize_Switch_Control( $wp_customize,
	    	'vmagazine_wow_animation_option', 
	            array(
	                'type' 			=> 'switch',	                
	                'label' 		=> esc_html__( 'Animation Option', 'vmagazine-lite' ),
	                'description' 	=> esc_html__( 'Enable/Disable wow animation on homepage.', 'vmagazine-lite' ),
	                'section' 		=> 'vmagazine_additional_home',
	                'choices'   	=> array(
	                    'enable' 	=> esc_html__( 'Enable', 'vmagazine-lite' ),
	                    'disable' 	=> esc_html__( 'Disable', 'vmagazine-lite' )
	                    ),
	                'priority'  	=> 40,
	            )	            	
	        )
	    );
        
       /*------------------------------------------------------------------------------------*/
		/**
		 * Fallback image option
		 */
		$wp_customize->add_section( 'vmagazine_fallback_img_section',array(
	            'title'		=> esc_html__( 'Fallback Image Settings', 'vmagazine-lite' ),
	            'panel'     => 'vmagazine_additional_settings_panel',
	            'priority'  => 15,
	        )
	    );

	    /**
	     * Fallback image option
	     *
	     * @since 1.0.0
	     */
	    $wp_customize->add_setting( 'post_fallback_img_option',array(
	            'default' 			=> 'show',
	            'sanitize_callback' => 'vmagazine_sanitize_switch_option',
	        )
	    );
	    $wp_customize->add_control( new Vmagazine_lite_Customize_Switch_Control($wp_customize, 
	    	'post_fallback_img_option',
	            array(
	                'type' 			=> 'switch',
	                'label' 		=> esc_html__( 'Fallback Image Option', 'vmagazine-lite' ),
	                'description' 	=> esc_html__( 'Show/Hide option of fallback image.', 'vmagazine-lite' ),
	                'section' 		=> 'vmagazine_fallback_img_section',
	                'choices'   	=> array(
	                    'show' 		=> esc_html__( 'Show', 'vmagazine-lite' ),
	                    'hide' 		=> esc_html__( 'Hide', 'vmagazine-lite' )
	                ),
	                'priority'  	=> 5,
	            )
	        )
	    );

	    /**
	     * Upload image control for fallback image
	     *
	     * @since 1.0.0
	     */
	    $wp_customize->add_setting('post_fallback_image',array(
	            'capability' 		=> 'edit_theme_options',
	            'sanitize_callback' => 'esc_url_raw'
	        )
	    );
	    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize,
	        'post_fallback_image',
	        	array(
	            	'label'      		=> esc_html__( 'Fallback Image', 'vmagazine-lite' ),
	               	'section'    		=> 'vmagazine_fallback_img_section',
	               	'priority' 			=> 10,
	               	'active_callback'	=> 'vmagazine_lite_fallback_option_callback'
	           	)
	       	)
	   	);

        /**
        * Lazyload images
        */
        $wp_customize->add_setting( 'vmagazine_lazyload_option', array(
	            'default' 			=> 'enable',
	            'sanitize_callback' => 'vmagazine_sanitize_switch_enable_option',
	            )
	    );
	    $wp_customize->add_control( new vmagazine_lite_Customize_Switch_Control( $wp_customize,
	    	'vmagazine_lazyload_option', 
	            array(
	                'type' 			=> 'switch',	                
	                'label' 		=> esc_html__( 'Lazy Load Images', 'vmagazine-lite' ),
	                'description' 	=> esc_html__( 'Enable/Disable lazy load for images.', 'vmagazine-lite' ),
	                'section' 		=> 'vmagazine_additional_home',
	                'choices'   	=> array(
	                    'enable' 	=> esc_html__( 'Enable', 'vmagazine-lite' ),
	                    'disable' 	=> esc_html__( 'Disable', 'vmagazine-lite' )
	                    ),
	                'priority'  	=> 41,
	            )	            	
	        )
	    );
/*------------------------------------------------------------------------------------*/
		/**
		 * Categories Color
		 */
		$wp_customize->add_section('vmagazine_categories_color_section',array(
	            'title'		=> esc_html__( 'Categories Color', 'vmagazine-lite' ),
	            'panel'     => 'vmagazine_additional_settings_panel',
	            'priority'  => 20,
	        )
	    );

	    global $vmagazine_cat_array;
	    foreach ( $vmagazine_cat_array as $key => $value ) {
	    	/**
	         * Theme color option
	         */
	        $wp_customize->add_setting('vmagazine_cat_color_'.$key, array(
	                'default'           => '#e52d6d',
	                'transport' 		=> 'postMessage',
	                'sanitize_callback' => 'sanitize_hex_color',
	            )
	        );
	        $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize,
	            'vmagazine_cat_color_'.$key,
	                array(
	                    'label'         => esc_html( $value ),
	                    'section'       => 'vmagazine_categories_color_section',
	                    'priority'      => 5
	                )
	            )
	        );
	    }

	}//close function
endif;

