<?php
/**
 * Plugin Name:  Colby Libraries / Post Slider
 * Plugin URI:   https://github.com/mikejandreau/post-slider
 * Description:  Plugin to display recent post excerpts as a slideshow. Simply add [post_slider] to the content area in WordPress.
 * Version:      1.0
 * Author:       Mike Jandreau
 * Author URI:   https://www.mikejandreau.net/
 * Text Domain:  slider
 * Licence:      GNU General Public License v2
 *
 * @package colbylibraries/post-slider
 */

namespace ColbyLibraries\PostSlider;

define( __NAMESPACE__ . '\VERSION', '1.0' );

add_action( 'wp_enqueue_scripts', 'ColbyLibraries\\PostSlider\\post_slider_assets' );
add_shortcode( 'post_slider', 'ColbyLibraries\\PostSlider\\post_slider' );

/**
 * Enqueues scripts and styles.
 */
function post_slider_assets() {
	// Register styles.
	wp_register_style(
		'colbylibraries/post-slider',
		plugins_url( 'dist/post-slider.css', __FILE__ ),
		array(),
		__NAMESPACE__ . '\VERSION'
	);
	wp_enqueue_style( 'colbylibraries/post-slider' );

	// Register scripts.
	wp_register_script(
		'colbylibraries/post-slider',
		plugins_url( 'dist/post-slider.js', __FILE__ ),
		array(),
		__NAMESPACE__ . '\VERSION',
		true
	);
	wp_enqueue_script( 'colbylibraries/post-slider' );
}

/**
 * Outputs CSS classes for a slider item.
 *
 * @param int $counter The index of the current item.
 */
function post_slider_item_class( &$counter ) {
	$counter++;

	echo esc_attr( 'postslider-item' );
	echo esc_attr( " item-$counter" );

	if ( 1 === $counter ) {
		echo esc_attr( ' currentSlide' );
	}
}

/**
 * Creates the WP_Query to retreive slider posts.
 *
 * @param int $get_this_many The number of posts to query.
 * @return WP_Query The query.
 */
function get_post_slider_query( $get_this_many = 3 ) {
	return new \WP_Query(
		array(
			'showposts'    => $get_this_many,
			'meta_key'     => '_thumbnail_id', // Only get posts with a featured image.
			'offset'       => 0,  // Set this to 1 to skip over first post, 2 to skip the first two, etc.
			'order'        => 'DESC', // Puts new posts first, to put oldest posts first, change to 'ASC'.
			'post__not_in' => get_option( 'sticky_posts' ), // Ignore sticky posts for this particular query.
		)
	);
}

/**
 * Outputs inline styles for a slideritem.
 *
 * @param string $thumb_url The image URL.
 */
function post_slider_item_style( $thumb_url ) {
	echo esc_attr(
		"background: linear-gradient(transparent, rgba(0, 0, 0, 0.6)), url('$thumb_url')"
	);
}

/**
 * Gets the thumbnail URL for the current slider item.
 *
 * @return string  The image URL.
 */
function get_post_slider_thumb_url() {
	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_id() ), 'large' );

	return is_array( $thumb ) && ! empty( $thumb[0] ) ? $thumb[0] : '';
}

/**
 * The [post_slider] shortcode callback.
 *
 * @return string The rendered HTML.
 */
function post_slider() {
	$counter      = 0; // Counter for posts.
	$recent_posts = get_post_slider_query();

	ob_start(); ?>

<div class="postslider">
	<div class="postslider-inner">
		<?php while ( $recent_posts->have_posts() ) : ?>
		<?php $recent_posts->the_post(); ?>
		<div class="<?php post_slider_item_class( $counter ); ?>"
			style="<?php post_slider_item_style( get_post_slider_thumb_url() ); ?>">
			<div class="postslider-item-content">
				<p class="postslider-heading" >
					<a href="<?php the_permalink(); ?>">
						<?php the_title(); ?>
					</a>
				</p>
				<?php the_excerpt(); ?>
				<a class="postslider-button" href="<?php the_permalink(); ?>">
					Read More
				</a>
			</div>
		</div>
		<?php endwhile; ?>
	</div>
	<span class="arrow arrow-prev"></span>
	<span class="arrow arrow-next"></span>
</div>

	<?php

	return ob_get_clean();
}
