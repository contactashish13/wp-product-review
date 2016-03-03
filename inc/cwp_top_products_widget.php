<?php
/**
 * CWP - Top Producs Widget
 */



class cwp_top_products_widget extends WP_Widget {



function __construct() {

parent::__construct(

'cwp_top_products_widget',

__('CWP Top Products Widget', 'cwppos'),



// Widget description

array( 'description' => __( 'This widget displays the top products based on their rating.', 'cwppos' ), )

);



}


	public function assets(){

		wp_enqueue_style( 'cwp-pac-widget-stylesheet',  WPPR_URL.'/css/cwppos-widget.css' );
		wp_enqueue_script( 'cwp-pac-main-script', WPPR_URL.'/javascript/main.js',array("jquery",'pie-chart'),WPPR_LITE_VERSION,true );
		wp_enqueue_script( 'pie-chart', WPPR_URL.'/javascript/pie-chart.js',array("jquery"), WPPR_LITE_VERSION,true );
        // Added by Ash/Upwork
        wp_enqueue_style( 'cwp-widget-stylesheet1',  WPPR_URL.'/css/cwppos-widget-style1.css' );
        wp_enqueue_style( 'cwp-widget-rating',  WPPR_URL.'/css/cwppos-widget-rating.css' );
        // Added by Ash/Upwork
	}
    // Added by Ash/Upwork
    public function adminAssets(){
        if(is_admin()){
            wp_enqueue_script( 'cwp-widget-script', WPPR_URL.'/javascript/widget.js');
            wp_localize_script("cwp-widget-script", "cwpw", array(
                "layout"    => $this->get_field_id( 'cwp_tp_layout' ),
                "rating"    => "ratingPara",
            ));
        }
    }
    // Added by Ash/Upwork
	public function custom_order_by($orderby){

		return 'mt1.meta_value DESC, mt2.meta_value+0 DESC';
	}
	public function widget( $args, $instance ) {
		$this->assets();
		if ( isset( $instance[ 'title' ]) )

			$title = apply_filters( 'widget_title', $instance['title'] );

		if ( isset( $instance[ 'no_items' ]) )

			$no_items = apply_filters( 'widget_content', $instance['no_items'] );

		if ( isset( $instance[ 'cwp_tp_category' ]) )

			$cwp_tp_category = apply_filters( 'widget_content', $instance['cwp_tp_category'] );

		if ( isset( $instance[ 'title_type' ]) )

			$post_type = apply_filters( 'widget_content', $instance['title_type'] );

		if ( isset( $instance[ 'show_image' ]) )

			$show_image = apply_filters( 'widget_content', $instance['show_image'] );



		// before and after widget arguments are defined by themes

		//echo "<div id='cwp_top_products_widget'>";

		echo $args['before_widget'];

		if ( ! empty( $title ) )

		echo $args['before_title'] . $title . $args['after_title'];


		if ( $cwp_tp_category=="All") $cwp_tp_category="";
	// Loop to get the most popular posts, ordered by the author's final grade.

		$query_args = array(

						'posts_per_page'=> $no_items, // limit it to the specified no of posts
						'post_type'	=>	"any",
						'post__not_in' => get_option('sticky_posts'),
						'category_name' => $cwp_tp_category,
						'meta_key'=> 'cwp_meta_box_check',
						'meta_query' => array(
											array(
													'key' => 'cwp_meta_box_check',
													'value'=> 'Yes',
												),
											array(
													'key'       => 'option_overall_score'
			                                    ),

											)
						);
		add_filter('posts_orderby',array($this,'custom_order_by'));
		$cwp_products_loop = new WP_Query( $query_args );
		remove_filter('posts_orderby',array($this,'custom_order_by'));
		//echo $cwp_top_products_loop->request;

        // Added by Ash/Upwork
        include trailingslashit(dirname(__FILE__)) . "/widget-layouts/" . $instance['cwp_tp_layout'];
        // Added by Ash/Upwork

		echo $args['after_widget'];

		//echo "</div>"; // end #cwp_top_products_widget

	}



	// Widget Backend

	public function form( $instance ) {
        // Added by Ash/Upwork
        $this->adminAssets();
        // Added by Ash/Upwork

		if ( isset( $instance[ 'title' ] ) ) {

			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Top Products', 'cwppos' );
		}
		if ( isset( $instance[ 'no_items' ]) ) {
			$no_items = $instance[ 'no_items' ];
		}
		else {
			$no_items = __( '10', 'cwppos');
		}
		if ( isset( $instance[ 'cwp_tp_category' ]) ) {
			$cwp_tp_category = $instance[ 'cwp_tp_category' ];}
		else {
			$cwp_tp_category = "Select Category";
		}
		if ( isset( $instance[ 'title_type' ]) ) {
			$title_type = $instance[ 'title_type' ];

		}
		else {
			$title_type = false;
		}

		if ( isset( $instance[ 'show_image' ]) ) {
			$show_image = $instance[ 'show_image' ];

		}
		else {
			$show_image = false;
		}


        // Added by Ash/Upwork
        $cwp_tp_buynow          = __("Buy Now", "cwppos");
        if ( isset( $instance[ 'cwp_tp_buynow' ]) ) {
			$cwp_tp_buynow  = $instance[ 'cwp_tp_buynow' ];
        }

        $cwp_tp_readreview      = __("Read Review", "cwppos");
        if ( isset( $instance[ 'cwp_tp_readreview' ]) ) {
			$cwp_tp_readreview  = $instance[ 'cwp_tp_readreview' ];
        }

        $cwp_tp_layout          = "";
        if ( isset( $instance[ 'cwp_tp_layout' ]) ) {
			$cwp_tp_layout  = $instance[ 'cwp_tp_layout' ];
        }

        $cwp_tp_rating_type     = "round";
        if ( isset( $instance[ 'cwp_tp_rating_type' ]) ) {
			$cwp_tp_rating_type = $instance[ 'cwp_tp_rating_type' ];
        }
        // Added by Ash/Upwork


		$cwp_tp_categ_array = get_categories('hide_empty=0');

		foreach ($cwp_tp_categ_array as $categs) {

			$cwp_tp_all_categories[$categs->slug] = $categs->name;

		}



	// Widget admin form

	?>

	<p>

	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', "cwppos" ); ?></label>

	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

	</p>



	<p>

	<label for="<?php echo $this->get_field_id( 'no_items' ); ?>"><?php _e( 'Number of posts to show:', "cwppos" ); ?></label>

	<input id="<?php echo $this->get_field_id( 'no_items' ); ?>" name="<?php echo $this->get_field_name( 'no_items' ); ?>" size="3" type="text" value="<?php echo esc_attr( $no_items ); ?>" />

	</p>



	<p>

	<?php $cwp_tp_selected_categ = esc_attr( $cwp_tp_category ); ?>

	<label for="<?php echo $this->get_field_id( 'cwp_tp_category' ); ?>"><?php _e( 'Category:', "cwppos" ); ?></label>

	<select id="<?php echo $this->get_field_id( 'cwp_tp_category' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_category' ); ?>">
	<?php echo "<option>All</option>"; ?>

	<?php foreach ($cwp_tp_all_categories as $categ_slug => $categ_name): ?>

			<?php if($categ_slug == $cwp_tp_selected_categ) {

				echo "<option selected>".$categ_slug."</option>";

			} elseif($categ_slug == "") {

				echo "<option>There are no categs</select>";

			} else {

				echo "<option>".$categ_slug."</option>";

			} ?>

	<?php endforeach; ?>

	</select>

	</p>
    <?php // Added by Ash/Upwork ?>

	<p>

	<?php $cwp_tp_buynow = esc_attr( $cwp_tp_buynow ); ?>

	<label for="<?php echo $this->get_field_id( 'cwp_tp_buynow' ); ?>"><?php _e( 'Buy Now text:', "cwppos" ); ?></label>

	<input id="<?php echo $this->get_field_id( 'cwp_tp_buynow' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_buynow' ); ?>" class="widefat" type="text" value="<?php echo $cwp_tp_buynow; ?>" />

	</p>

	<p>

	<?php $cwp_tp_readreview = esc_attr( $cwp_tp_readreview ); ?>

	<label for="<?php echo $this->get_field_id( 'cwp_tp_readreview' ); ?>"><?php _e( 'Read Review text:', "cwppos" ); ?></label>

	<input id="<?php echo $this->get_field_id( 'cwp_tp_readreview' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_readreview' ); ?>" class="widefat" type="text" value="<?php echo $cwp_tp_readreview; ?>" />

	</p>

	<p>

	<?php $cwp_tp_layout = esc_attr( $cwp_tp_layout ); ?>

	<label for="<?php echo $this->get_field_id( 'cwp_tp_layout' ); ?>"><?php _e( 'Layout:', "cwppos" ); ?></label>

	<select id="<?php echo $this->get_field_id( 'cwp_tp_layout' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_layout' ); ?>">

	<?php 

        $layouts            = array();
        $customLayoutFiles  = glob(trailingslashit(dirname(__FILE__)) . "widget-layouts/*.php");
        foreach($customLayoutFiles as $file){
            $layouts[basename($file)] = ucwords(basename($file, ".php"));
        }
    
        foreach ($layouts as $key => $val):
            $extra      = "";
            if($key == $cwp_tp_layout) $extra = "selected";

            echo "<option value='{$key}' {$extra}>{$val}</option>";
	    endforeach;
    ?>

	</select>

	</p>

	<p id="ratingPara" style="display: none">

	<?php $cwp_tp_rating_type = esc_attr( $cwp_tp_rating_type ); ?>

	<label for="<?php echo $this->get_field_id( 'cwp_tp_rating_type' ); ?>"><?php _e( 'Rating Type:', "cwppos" ); ?></label>

	<select id="<?php echo $this->get_field_id( 'cwp_tp_rating_type' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_rating_type' ); ?>">

	<?php 

        $ratingTypes    = array(
            "round"     => __("Round", "cwppos"),
            "star"      => __("Star", "cwppos"),
        );
    
        foreach ($ratingTypes as $key => $val):
            $extra      = "";
            if($key == $cwp_tp_rating_type) $extra = "selected";

            echo "<option value='{$key}' {$extra}>{$val}</option>";
	    endforeach;
    ?>

	</select>

	</p>

    <?php // Added by Ash/Upwork ?>
    <p>

	<label for="<?php echo $this->get_field_id( 'title_type' ); ?>"><?php _e( 'Display Product Titles :', "cwppos" ); ?></label>

	<input  id="<?php echo $this->get_field_id( 'title_type' ); ?>" name="<?php echo $this->get_field_name( 'title_type' ); ?>"  type="checkbox" <?php checked( $title_type ); ?>  />
	</p>

	<p>

	<label for="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php _e( 'Display Product Image :', "cwppos" ); ?></label>

	<input  id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>"  type="checkbox" <?php checked( $show_image ); ?>  />
	</p>


	<?php }



	// Updating widget replacing old instances with new

	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		$instance['no_items'] = ( ! empty( $new_instance['no_items'] ) ) ? strip_tags( $new_instance['no_items'] ) : '';

		$instance['cwp_tp_category'] = ( ! empty( $new_instance['cwp_tp_category'] ) ) ? strip_tags( $new_instance['cwp_tp_category'] ) : '';

		$instance['title_type'] = (bool) $new_instance['title_type'] ;
		$instance['show_image'] = (bool) $new_instance['show_image'] ;

        // Added by Ash/Upwork
        $instance['cwp_tp_buynow'] = ( ! empty( $new_instance['cwp_tp_buynow'] ) ) ? strip_tags( $new_instance['cwp_tp_buynow'] ) : '';
        $instance['cwp_tp_readreview'] = ( ! empty( $new_instance['cwp_tp_readreview'] ) ) ? strip_tags( $new_instance['cwp_tp_readreview'] ) : '';
        $instance['cwp_tp_layout'] = ( ! empty( $new_instance['cwp_tp_layout'] ) ) ? strip_tags( $new_instance['cwp_tp_layout'] ) : '';
        $instance['cwp_tp_rating_type'] = ( ! empty( $new_instance['cwp_tp_rating_type'] ) ) ? strip_tags( $new_instance['cwp_tp_rating_type'] ) : '';
        // Added by Ash/Upwork
		return $instance;

	}



} // end Class cwp_top_products_widget





// Register and load the widget

function cwp_load_top_products_widget() {

	register_widget( 'cwp_top_products_widget' );

}

add_action( 'widgets_init', 'cwp_load_top_products_widget' );