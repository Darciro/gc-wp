<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package AccessPress Themes
 * @subpackage Vmagazine-lite
 * @since 1.0.0
 */

get_header(); 
?>
	 <div class="vmagazine-container">
	 	<?php
		do_action( 'vmagazine_lite_before_body_content' );

		while ( have_posts() ) : 
			the_post();
			$post_id = get_the_ID();

			get_template_part( 'layouts/post/single', 'layout1' );
			/**
			 * Set post view
			 */
			if( function_exists('vmagazine_setPostViews')){
				vmagazine_setPostViews( get_the_ID() );	
			}
			

		endwhile; // End of the loop.

		do_action( 'vmagazine_lite_after_lite_body_content' );
?>
</div>
<?php

get_footer();
