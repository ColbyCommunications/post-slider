<?php
/**
* Plugin Name:  Post Slider
* Plugin URI:   https://github.com/mikejandreau/post-slider
* Description:  Plugin to display recent post excerpts as a slideshow. Simply add [post_slider] to the content area in WordPress.
* Version:      1.0
* Author:       Mike Jandreau
* Author URI:   https://www.mikejandreau.net/
* Text Domain:  slider
* Licence:      GNU General Public License v2
*
*/

function post_slider_assets() {
    // Register styles
    wp_register_style('post_slider_styles', plugins_url('css/styles.css',__FILE__ ));
    wp_enqueue_style('post_slider_styles');

    // Register scripts
    wp_register_script( 'post_slider_scripts', plugins_url('js/scripts.js', __FILE__), array(),'1.1', true);
    wp_enqueue_script('post_slider_scripts');
}
add_action( 'wp_enqueue_scripts','post_slider_assets');

// Shortcode to display slider on any page or post
function post_slider(){ 
    ob_start(); ?> 

    <div class="postslider">
        <div class="postslider-inner">
            <?php
                $counter = 0; // Counter for posts
                $getThisMany = 3; // Number of posts to pull
                $recentPosts = new WP_Query(array(
                    'showposts' => $getThisMany, 
                    'meta_key' => '_thumbnail_id', // Only get posts with a featured image
                    'offset' => 0,  // Set this to 1 to skip over first post, 2 to skip the first two, etc.
                    'order' => 'DESC', // Puts new posts first, to put oldest posts first, change to 'ASC'
                    'post__not_in' => get_option("sticky_posts"), // Ignore sticky posts for this particular query
                ));
            ?>
            <?php while ($recentPosts->have_posts()) : $recentPosts->the_post(); ?>
                <?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_id()), 'large' ); ?>
                <div class="postslider-item item-<?php echo $counter++; ?> <?php if ($counter == 1) echo 'currentSlide'; ?>" style="background: linear-gradient(transparent, rgba(0, 0, 0, 0.6)),url('<?php echo $thumb['0'];?>')">
                    <div class="postslider-item-content">
                        <p class="postslider-heading" ><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                        <?php the_excerpt(); ?>
                        <a class="postslider-button" href="<?php the_permalink(); ?>">Read More</a>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <span class="arrow arrow-prev"></span>
        <span class="arrow arrow-next"></span>
    </div>

    <?php return ob_get_clean();
}
add_shortcode('post_slider', 'post_slider');
?>