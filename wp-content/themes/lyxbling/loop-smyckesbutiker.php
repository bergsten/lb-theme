<?php
/**
 * Loop - Portfolio
 *
 * This is a custom loop file, containing the looping logic for use in the "portfolio" page template, 
 * as well as the "portfolio-gallery" taxonomy archive screens. The custom query is only run on the page
 * template, as we already have the data we need when on the taxonomy archive screens.
 *
 * @package WooFramework
 * @subpackage Template
 */

global $woo_options;
global $more; $more = 0;

/* Setup parameters for this loop. */
/* Make sure we only run our custom query when using the page template, and not in a taxonomy. */

$thumb_width = 300;
$thumb_height = 300;

if ( ! is_tax() ) {

$butiker = array();
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; 
$query_args = array(
				'post_type' => 'smyckesbutiker', 
                                'orderby' => 'title',
                                'order' => 'ASC',
				'paged' => $paged, 
                                'post_status' => 'publish',
				'posts_per_page' => -1
			);

/* Setup portfolio galleries navigation. */
$butiker = get_terms( 'butik' );

$exclude_str = '';
if ( isset( $woo_options['woo_portfolio_excludenav'] ) && ( $woo_options['woo_portfolio_excludenav'] != '' ) ) {
	$exclude_str = $woo_options['woo_portfolio_excludenav'];
}

// Allow child themes/plugins to filter here.
$exclude_str = apply_filters( 'woo_portfolio_gallery_exclude', $exclude_str );

/* Optionally exclude navigation items. */
if ( $exclude_str != '' ) {
	$to_exclude = explode( ',', $exclude_str );
	
	if ( is_array( $to_exclude ) ) {
		foreach ( $to_exclude as $k => $v ) {
			$to_exclude[$k] = str_replace( ' ', '', $v );
		}
		
		/* Remove the galleries to be excluded. */
		foreach ( $butiker as $k => $v ) {
			if ( in_array( $v->slug, $to_exclude ) ) {
				unset( $butiker[$k] );
			}
		}
	}
}

/* If we have galleries, make sure we only get items from those galleries. */
if ( count( $butiker ) > 0 ) {

$gallery_slugs = array();
foreach ( $butiker as $g ) { $gallery_slugs[] = $g->slug; }

$query_args['tax_query'] = array(
								array(
									'taxonomy' => 'butik',
									'field' => 'slug',
									'terms' => $gallery_slugs
								)
							);
}

/* The Query. */			   
query_posts( $query_args );

} // End IF Statement ( is_tax() )

/* The Loop. */	
if ( have_posts() ) { $count = 0;
?>
<div id="portfolio">
        <h1 class="entry-title">Smyckesbutiker</h1>
        <p>Här hittar du de smyckesbutiker online vi recenserar för att hjälpa dig välja den smyckesbutik som passar dig bäst.</p>
        <ul>
            <li class="sort lb-button small" data-sort="data-name" data-order="desc" rel="nolink">Butiksnamn A - Ö</li>
            <li class="sort lb-button small" data-sort="data-name" data-order="asc" rel="nolink">Butiksnamn Ö - A</li>
        </ul>
	<div class="portfolio-items">
            <ul id="Grid">
<?php
            while ( have_posts() ) {
                the_post(); $count++;

                /* Get the settings for this portfolio item. */
                $settings = woo_portfolio_item_settings( $post->ID );

                /* If the theme option is set to link to the single portfolio item, adjust the $settings. */
                if ( isset( $woo_options['woo_portfolio_linkto'] ) && ( $woo_options['woo_portfolio_linkto'] == 'post' ) ) {
                        $settings['large'] = get_permalink( $post->ID );
                        $settings['rel'] = '';
                } ?>
                <li class="mix" data-name="<?php the_title(); ?>">
                    <div class="view view-tenth hover_effect"><?php
                        /* Setup image for display and for checks, to avoid doing multiple queries. */
                        $image = woo_image( 'return=true&key=portfolio-image&meta=' . get_the_title() . '&width=' . $thumb_width . '&height=' . $thumb_height . '&link=img&alt=' . the_title_attribute( array( 'echo' => 0 ) ) . '' ); ?>

                        <?php echo $image; ?>
                        <div class="mask">
                            
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <p><?php echo(lb_trim_text(get_the_excerpt(), 350)); ?></p>
                                <div class="lb-button small" style="clear: both;" data-url="<?php the_permalink(); ?>"><a href="<?php the_permalink(); ?>">Läs mer &raquo;</a></div>
                            </a>
                        </div>
                    </div>
                </li><?php
            } // End WHILE Loop
    ?>
            </ul>
        </div><!--/.portfolio-items-->
</div><!--/#portfolio-->
<?php
} else {
	get_template_part( 'content', 'noposts' );
} // End IF Statement

rewind_posts();

woo_pagenav();
?>