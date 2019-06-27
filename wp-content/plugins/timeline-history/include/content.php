<?php
if ( ! defined('ABSPATH') ) {
	die('Please do not load this file directly!');
}
$args = array( 'post_type' => 'history_post', 'posts_per_page' => -1, 'meta_key' => 'history-date',
            'orderby' => 'meta_value', 'order' => 'ASC' );
				$loop = new WP_Query( $args );
				/*$i = '01/01/2000';*/
				while ( $loop->have_posts() ) : $loop->the_post();
					$history_date = get_post_meta( get_the_ID(), 'history-date', true );
					$date = date_create($history_date);
				        $date_formate = date_format($date,"Y/m/d");
				        $history_date_value = date("d/m/Y", strtotime($date_formate));
					$history_top_title = esc_html(get_post_meta( get_the_ID(), 'history_top_title', true ));
					$my_content = apply_filters('the_content',get_the_content());
					$titulo_bio = apply_filters('the_title',get_the_title());
					$img = explode('src="',$my_content);
					$img = explode('"',$img[1]);
					$img = $img[0];
					$content .='<li data-date="'.esc_attr($history_date_value).'">
						<div class="bio-img" Style="height:425px; width:350px;background-image: url(\''.$img.'\');">
							<div class="bio-txt">'.get_the_title().'</div>
						<div/>
					</li>';												
				endwhile; ?>
					 
