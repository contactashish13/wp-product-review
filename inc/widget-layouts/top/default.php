<?php
        echo "<ul>";

		while($cwp_top_products_loop->have_posts()) : $cwp_top_products_loop->the_post();

			$product_image = wppr_get_image_id(get_the_ID(),get_post_meta(get_the_ID(), "cwp_rev_product_image", true),'wppr_widget_image');
			$product_title = ($post_type==true) ? get_post_meta($cwp_top_products_loop->post->ID, "cwp_rev_product_name", true)  :  get_the_title();
			?>



		<li class="cwp-popular-review cwp_top_posts_widget_<?php the_ID(); if ($show_image==true&&!empty($product_image)) echo ' wppr-cols-3'; else echo ' wppr-cols-2' ?>">
		<?php

		if ($show_image==true&&!empty($product_image)) {
		?>

		<img class="cwp_rev_image wppr-col" src="<?php echo $product_image;?>" alt="<?php echo $product_title; ?>"\>
		<?php } ?>
		<a href="<?php the_permalink(); ?>" class="wppr-col" title="<?php echo $product_title; ?>">

			<?php echo $product_title; ?>

		</a>





			<?php

            for($i=1; $i<6; $i++) {
                ${"option".$i."_content"} = get_post_meta($cwp_top_products_loop->post->ID, "option_".$i."_content", true);
                //if(empty(${"option".$i."_content"})) { ${"option".$i."_content"} = __("Default Feature ".$i, "cwppos"); }
            }
            $review_score = cwppos_calc_overall_rating($cwp_top_products_loop->post->ID);
			$review_score = $review_score['overall'];

			if(!empty($review_score)) { ?>

			<div class="review-grade-widget wppr-col">

				<div class="cwp-review-chart">

				<div class="cwp-review-percentage" data-percent="<?php echo $review_score; ?>"><span></span></div>

				</div><!-- end .chart -->

			</div>

			<?php } ?>

		</li><!-- end .popular-review -->



		<?php endwhile; ?>

		<?php wp_reset_postdata(); // reset the query



		echo "</ul>";