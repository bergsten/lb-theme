<?php
/**
 * Tävlingar Content Template
 *
 * This template is the default tavlingar content template. It is used to display the content of the
 * `tavlingar.php` template file, contextually, as well as in archive lists or search results.
 *
 * @package WooFramework
 * @subpackage Template
 */

/**
 * Settings for this template file.
 *
 * This is where the specify the HTML tags for the title.
 * These options can be filtered via a child theme.
 *
 * @link http://codex.wordpress.org/Plugin_API#Filters
 */

$settings = array(
				'thumb_w' => 150,
				'thumb_h' => 150,
				'thumb_align' => 'alignleft',
				'post_content' => 'excerpt',
				'comments' => 'both'
				);

$title_before = '<h1 class="title rabattkoder entry-title">';
$title_after = '</h1>';

if ( ! is_single() ) {
    $title_before = '<h2 class="title tavlingar entry-title">';
    $title_after = '</h2>';
    $article_url = esc_url( get_permalink( get_the_ID() ) );
    $article_link = '<a href="' . $article_url . '" rel="bookmark">';
    $title_before = $title_before . $article_link;
    $title_after = '</a>' . $title_after;
}

$page_link_args = apply_filters( 'woothemes_pagelinks_args', array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) );

woo_post_before();
?>
<article <?php post_class(); ?>>
<?php
woo_post_inside_before();

if ( ! is_singular() ) {
?>
    <section class="image">
<?php 
woo_image( 'width=' . esc_attr( $settings['thumb_w'] ) . '&height=' . esc_attr( $settings['thumb_h'] ) . '&class=thumbnail ' . esc_attr( $settings['thumb_align'] ) );
?>
    </section>
<?php
}
?>
	<section class="entry">
            <header>
                <?php the_title( $title_before, $title_after ); ?>
            </header>
<?php
if ( 'content' == $settings['post_content'] || is_single() ) { 
    $is_percentage = get_post_meta($post->ID, 'wpcf-percentage', true);
    
    if(1 == $is_percentage) {
        $discount_amount = get_post_meta($post->ID, 'wpcf-discount-amount', true);
        $discount_image = '<a href="' . '/till/' . $post->ID . '" rel="external" target="_blank"><img class="alignright size-thumbnail" alt="' . get_the_title() . '" src="' . get_stylesheet_directory_uri() . '/images/rabattkoder/rabatt-' . $discount_amount . '.png" width="300" height="230" /></a>';
        echo($discount_image);
    } else {
        $store_post_id = get_post_meta($post->ID, 'wpcf-store-post-id', true);
        $store_url = get_post_permalink($store_post_id);
        $store_brand = get_post_meta($store_post_id, 'wpcf-varumarke', true);

        if (has_post_thumbnail($store_post_id)) {
            $store_image = wp_get_attachment_image_src(get_post_thumbnail_id($store_post_id), 'thumbnail');
            $store_image_html = '<a href="' . $store_url . '"><img class="alignright size-thumbnail" alt="' . $store_brand . ' smycken online" src="' . $store_image[0] . '" width="' . $store_image[1] . '" height="' . $store_image[2] . '" /></a>';
            echo($store_image_html);
        }
    }
    
    the_content( __( 'Continue Reading &rarr;', 'woothemes' ) );
    echo(lb_get_link_button(get_the_ID()));
} else { 
    // Remove ShareThis.
    remove_filter('get_the_excerpt', 'st_remove_st_add_link', 9);
    remove_filter('the_excerpt', 'st_add_widget');
    the_excerpt(); 
    //do_shortcode( '[button color="pink"]Läs mer &raquo;[/button]' );
    ?>
    <div class="lb-button right" data-url="<?php echo($article_url); ?>"><?php _e( 'Continue Reading', 'woothemes' ); ?> om erbjudandet &raquo;</div>
    <?php
}
if ( 'content' == $settings['post_content'] || is_singular() ) wp_link_pages( $page_link_args );
?>
	</section><!-- /.entry -->
	<div class="fix"></div>
<?php
woo_post_inside_after();
?>
</article><!-- /.post -->
<?php
woo_post_after();
$comm = $settings['comments'];
if ( ( 'post' == $comm || 'both' == $comm ) && is_single() ) { comments_template(); }
?>