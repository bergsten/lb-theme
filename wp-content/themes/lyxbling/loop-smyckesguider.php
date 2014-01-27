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

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; 
$query_args = array(
				'post_type' => 'smyckesguider', 
                                'orderby' => 'title',
                                'order' => 'ASC',
				'paged' => $paged, 
                                'post_status' => 'publish',
				'posts_per_page' => -1
			);


/* The Query. */			   
$the_query = new WP_Query($query_args);

} // End IF Statement ( is_tax() )

/* The Loop. */	
if ( $the_query->have_posts() ) { ?>
<div id="portfolio">
        <h1 class="entry-title">Smyckesguider</h1>
        <p>Här kan du lära dig mer om smycken, diamanter, ädelstenar, olika ädelmetaller, m.m.</p>
        <ul>
            <li class="sort lb-button small" data-sort="data-name" data-order="desc" rel="nolink">Smyckesguider A - Ö</li>
            <li class="sort lb-button small" data-sort="data-name" data-order="asc" rel="nolink">Smyckesguider Ö - A</li>
        </ul>
	<div class="portfolio-items">
            <ul id="Grid"><?php
            while ( $the_query->have_posts() ) {
                $the_query->the_post(); ?>
                <li class="mix" data-name="<?php the_title(); ?>">
                    <div class="view view-tenth lb-item hover" data-url="<?php the_permalink(); ?>"><?php
                        /* Setup image for display and for checks, to avoid doing multiple queries. */
                        $image = woo_image( 'return=true&class=hover_effect&key=portfolio-image&meta=' . get_the_title() . '&width=' . $thumb_width . '&height=' . $thumb_height . '&link=img&alt=' . the_title_attribute( array( 'echo' => 0 ) ) . '' ); ?>

                        <?php echo $image; ?>
                        <span class="img-title"><?php the_title(); ?></span>
                        <div class="mask hover_effect">
                                <h2 class="hover_effect"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <p class="hover_effect"><?php echo(lb_trim_text(get_the_excerpt(), 350)); ?></p>
                                <div class="lb-button small" style="clear: both;" data-url="<?php the_permalink(); ?>">Läs mer &raquo;</div>
                            </a>
                        </div>
                    </div>
                </li><?php
            } // End WHILE Loop ?>
            </ul>
        </div><!--/.portfolio-items-->
</div><!--/#portfolio--><?php
} else {
	get_template_part( 'content', 'noposts' );
} // End IF Statement

rewind_posts();

woo_pagenav();
?>