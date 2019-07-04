<?php
/**
 * WordPress unit test plugin.
 *
 * @package     WPPR
 * @subpackage  Tests
 * @copyright   Copyright (c) 2017, ThemeIsle
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 3.0.10
 */

/**
 * Class Test_WPPR
 */
class Test_WPPR extends WP_UnitTestCase {

	/**
	 * Create post of a particular post_type and see if it behaves like a review.
	 *
	 * @dataProvider postTypeDataProvider
	 */
	function test_post_review( $post_type ) {
		$is_cpt = 'wppr_review' === $post_type;
		$title  = 'Test Post' . rand();

		if ( $is_cpt ) {
			// enable the CPT feature.
			$model = new WPPR_Query_Model();
			$model->wppr_set_option( 'wppr_cpt', 'yes' );
		}

		do_action( 'init' );

		$p = $this->factory->post->create(
			array(
				'post_title' => $title,
				'post_type' => $post_type,
			)
		);

		$review = new WPPR_Review_Model( $p );

		// Check new Review is not active
		$this->assertFalse( $review->is_active() );
		// Activate Review for this post.
		$review->activate();
		// Check new Review is active
		$this->assertTrue( $review->is_active() );

		$options = array(
			array(
				'name' => 'Excellent',
				'value' => '100',
			),
			array(
				'name' => 'Great',
				'value' => '90',
			),
			array(
				'name' => 'Good',
				'value' => '80',
			),
			array(
				'name' => 'Average',
				'value' => '70',
			),
			array(
				'name' => 'Bad',
				'value' => '50',
			),
		);
		$review->set_options( $options );
		// Check rating is as expected
		$this->assertEquals( 78, $review->get_rating() );

		$review_data = $review->get_review_data();

		if ( ! $is_cpt ) {
			$review_data['name'] = 'Test param change';
			$review->set_name( $review_data['name'] );
		}
		$review_data['price'] = floatval( '10.00' );
		$review_data['price_raw'] = floatval( '10.00' );
		$review->set_price( $review_data['price'] );
		// Check Param save
		$this->assertEquals( $review_data, $review->get_review_data() );

		$settings = new WPPR_Options_Model();
		$settings->wppr_set_option( 'cwppos_option_nr', 5 );
		$review->wppr_set_option( 'cwppos_show_userreview', 'yes' );
		$this->assertEquals( 5, $settings->wppr_get_option( 'cwppos_option_nr' ) );
		$this->assertEquals( 'yes', $review->wppr_get_option( 'cwppos_show_userreview' ) );

		$c = $this->factory->comment->create(
			array(
				'comment_post_ID' => $p,
				'comment_content' => 'Test Comment',
				'status' => 'approve',
			)
		);
		add_comment_meta( $c, 'meta_option_1', 5.4 );
		add_comment_meta( $c, 'meta_option_2', 4.3 );
		add_comment_meta( $c, 'meta_option_3', 3.2 );
		add_comment_meta( $c, 'meta_option_4', 2.3 );
		add_comment_meta( $c, 'meta_option_5', 1.2 );
		$this->assertEquals( 3.04, $review->get_comments_rating() );
		$review->wppr_set_option( 'cwppos_infl_userreview', '30' );
		$this->assertEquals( '63.72', number_format( $review->get_rating(), 2 ) );

		if ( $is_cpt ) {
			// let's navigate to the review page and then see what is the name of the review.
			$this->go_to( get_permalink( $p ) );
			$this->assertEquals( $title, $review->get_name() );
		}
	}


	/**
	 * Creates a review post, sets the scale to 5, posts a comment and checks if it has been correctly
	 * transformed into a out-of-10 rating.
	 *
	 */
	function test_review_on_scale_of_5() {
		$title  = 'Test Post' . rand();

		do_action( 'init' );

		$p = $this->factory->post->create(
			array(
				'post_title' => $title,
				'post_type' => 'post',
			)
		);

		$review = new WPPR_Review_Model( $p );

		// Check new Review is not active
		$this->assertFalse( $review->is_active() );
		// Activate Review for this post.
		$review->activate();
		// Check new Review is active
		$this->assertTrue( $review->is_active() );

		$options = array(
			array(
				'name' => 'Excellent',
				'value' => '100',
			),
			array(
				'name' => 'Great',
				'value' => '90',
			),
			array(
				'name' => 'Good',
				'value' => '80',
			),
			array(
				'name' => 'Average',
				'value' => '70',
			),
			array(
				'name' => 'Bad',
				'value' => '50',
			),
		);
		$review->set_options( $options );
		// Check rating is as expected
		$this->assertEquals( 78, $review->get_rating() );

		$settings = new WPPR_Options_Model();
		$settings->wppr_set_option( 'cwppos_option_nr', 5 );
		$settings->wppr_set_option( 'wppr_use_5_rating_scale', 5 );
		$settings->wppr_set_option( 'cwppos_show_userreview', 'yes' );
		$this->assertEquals( 5, $settings->wppr_get_option( 'cwppos_option_nr' ) );
		$this->assertEquals( 5, $settings->wppr_get_option( 'wppr_use_5_rating_scale' ) );
		$this->assertEquals( 'yes', $settings->wppr_get_option( 'cwppos_show_userreview' ) );

		update_option('require_name_email', '');

		$_POST	= array(
			'comment_post_ID' => $p,
			'wppr-slider-option-0' => 1,
			'wppr-slider-option-1' => 2,
			'wppr-slider-option-2' => 3,
			'wppr-slider-option-3' => 4,
			'wppr-slider-option-4' => 5,
		);

		$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
		$meta = get_comment_meta( $comment->comment_ID );

		// the value stored will be on a scale of 10.
		$this->assertEquals( 2, $meta['meta_option_0'][0] );
		$this->assertEquals( 4, $meta['meta_option_1'][0] );
		$this->assertEquals( 6, $meta['meta_option_2'][0] );
		$this->assertEquals( 8, $meta['meta_option_3'][0] );
		$this->assertEquals( 10, $meta['meta_option_4'][0] );
	}

	/**
	 * Provide the different post_types to test with.
	 */
	public function postTypeDataProvider() {
		return array(
			array( 'post' ),
			array( 'wppr_review' ),
		);
	}
}
