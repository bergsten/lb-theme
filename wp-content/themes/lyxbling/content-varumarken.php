<?php
/**
 * Varumarken Content Template
 *
 * This template is the default varumarken content template. It is used to display the content of the
 * `varumarken.php` template file, contextually, as well as in archive lists or search results.
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

$title_before = '<h1 class="title tavlingar">';
$title_after = '</h1>';

if ( ! is_single() ) {
$title_before = '<h2 class="title tavlingar">';
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
?>
	<header>
	<?php the_title( $title_before, $title_after ); ?>
	</header>
        
<?php
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
<?php
if ( 'content' == $settings['post_content'] || is_single() ) { ?>
    <h2>Sammanfattning</h2>
    <?php
    the_content( __( 'Continue Reading &rarr;', 'woothemes' ) ); 
    ?>
    <h2>Fakta</h2>
    <?php
    $varumarken_url = get_site_meta_url(get_the_ID());
    
    echo('<p>URL: <a href="' . $varumarken_url["url"] . '" target="_blank">' . $varumarken_url["pretty_url"] . '</a></p>');
    echo(get_site_meta_fakta(get_the_ID()));
    
    $rabattkoder = get_related_posts_by_taxonomy(get_the_ID(), 'varumarke', 'rabattkoder');
    if($rabattkoder->post_count > 0) {
        echo('<h2>Aktuella rabattkoder for ' . get_the_title() . '</h2>');
        foreach($rabattkoder->posts as $the_post) {
            echo('<h3><a href="' . get_permalink($the_post->ID) . '">' . $the_post->post_title . '</a></h3>');
            echo(strip_tags($the_post->post_content)); ?>
            <br clear="all" /><a href="<?php echo(get_permalink($the_post->ID)); ?>" class="woo-sc-button lb-pink"><span class="woo-" style="text-align: right;"><?php _e( 'Continue Reading &rarr;', 'woothemes' ); ?></span></a><?php
        }
    }
    
    $tavlingar = get_related_posts_by_taxonomy(get_the_ID(), 'varumarke', 'tavlingar');
    if($tavlingar->post_count > 0) {
        echo('<h2>Aktuella tavlingar fran ' . get_the_title() . '</h2>');
        foreach($tavlingar->posts as $the_post) {
            echo('<h3><a href="' . get_permalink($the_post->ID) . '">' . $the_post->post_title . '</a></h3>');
            echo(strip_tags($the_post->post_content)); ?>
            <br clear="all" /><a href="<?php echo(get_permalink($the_post->ID)); ?>" class="woo-sc-button lb-pink"><span class="woo-" style="text-align: right;"><?php _e( 'Continue Reading &rarr;', 'woothemes' ); ?></span></a><?php
        }
    }
    
    if(function_exists('zemanta_related_posts'))
        zemanta_related_posts();
} else { 
    // Remove ShareThis.
    remove_filter('get_the_excerpt', 'st_remove_st_add_link', 9);
    remove_filter('the_excerpt', 'st_add_widget');
    the_excerpt(); 
    //do_shortcode( '[button color="pink"]L�s mer &raquo;[/button]' );
    ?>
    <a href="<?php echo($article_url); ?>" class="woo-sc-button lb-pink"><span class="woo-" style="text-align: right;"><?php _e( 'Continue Reading &rarr;', 'woothemes' ); ?></span></a>
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