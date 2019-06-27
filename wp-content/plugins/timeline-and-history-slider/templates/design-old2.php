<div id="wpostahs-slider-nav-<?php echo $unique; ?>" class="wpostahs-slider-nav-<?php echo $unique; ?> wpostahs-slider-nav wpostahs-slick-slider" <?php echo $slider_as_nav_for; ?>>
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
		<div class="wpostahs-slider-nav-title">
			<div class="wpostahs-main-title">
				<button></button>
			</div>
			<div class="wpostahs-title"><?php echo the_title(); ?></div>
		</div>
	<?php endwhile; ?>
</div>
<div class="wpostahs-slider-for-<?php echo $unique; ?> wpostahs-slider-for wpostahs-slick-slider">
	<?php while ( $query->have_posts() ) : $query->the_post(); ?>
		<?php $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>
		<div class="wpostahs-slider-nav-content">
			<div class="wpostahs-centent corner" style="background-image:url(); background-repeat:no-repeat; background-size:cover;">
				<div class="txt-bio corner">
					
				<?php echo the_content(); ?>	</div>	
				<img src="<?php echo $feat_image; ?>">
			</div>
		<?php if( !empty($feat_image) ) { ?>	
			<div style="text-align:center">
			</div>
		<?php } ?>	
		</div>
	<?php endwhile; ?>
</div><!-- #post-## -->