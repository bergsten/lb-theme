<?php
 
// Load the textdomain for translation
load_child_theme_textdomain( 'woothemes' );

/*-----------------------------------------------------------------------------------*/
/* Custom breadcrumbs with Yoast SEO plugin. */
/*-----------------------------------------------------------------------------------*/
function woo_custom_breadcrumbs () {
    if ( !is_front_page() && function_exists('yoast_breadcrumb') ) {    
        yoast_breadcrumb('<p id="breadcrumbs">','</p></br>');
    } 
}
add_action('woo_loop_before', 'woo_custom_breadcrumbs', 10 );

/*-----------------------------------------------------------------------------------*/
/* Single Post Author. From https://managewp.com/create-an-authority-site-part-2 */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_author_box' ) ) {
    function woo_author_box () {
      global $post;
      $author_id=$post->post_author;
    ?>
    <aside id="post-author">
            <div class="profile-image"><?php echo get_avatar( $author_id, '80' ); ?></div>
            <div class="profile-content">
                    <h4><a href="<?php echo get_the_author_meta( 'user_url', $author_id ); ?>"><?php printf( esc_attr__( 'About %s', 'woothemes' ), get_the_author_meta( 'display_name', $author_id ) ); ?></a></h4>
                    <?php echo get_the_author_meta( 'description', $author_id ); ?>
                    <?php if ( is_singular() ) { ?>
                    <div class="profile-link">
                            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ); ?>">
                                    <?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'woothemes' ), get_the_author_meta( 'display_name', $author_id ) ); ?>
                            </a>
                    </div><!--#profile-link-->
                    <?php } ?>
            </div>
            <div class="fix"></div>
    </aside>
    <?php
    } // End woo_author_box()
}

function change_yoast_title($yoast_title) {
    if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {
        /* Get the post type object. */
        $post_type_object = get_post_type_object( get_query_var( 'post_type' ) );

        $title = $post_type_object->labels->name;
    }
    
    return $title . ' ' . $yoast_title;
}

/*-----------------------------------------------------------------------------------*/
/* Remove 'Archive' from Post Author archive. From http://wpu.me/remove-the-word-archive-from-canvas-archive-header/ */
/*-----------------------------------------------------------------------------------*/
function woo_archive_title ( $before = '', $after = '', $echo = true ) {

	global $wp_query;

	if ( is_category() || is_tag() || is_tax() ) {

		$taxonomy_obj = $wp_query->get_queried_object();
		$term_id = $taxonomy_obj->term_id;
		$taxonomy_short_name = $taxonomy_obj->taxonomy;

		$taxonomy_raw_obj = get_taxonomy( $taxonomy_short_name );

	} // End IF Statement

	$title = '';
	$delimiter = ' | ';
	$date_format = get_option( 'date_format' );

	// Category Archive
	if ( is_category() ) {

		$title = '<span class="fl cat">' . single_cat_title( '', false ) . '</span> <span class="fr catrss">';
		$cat_obj = $wp_query->get_queried_object();
		$cat_id = $cat_obj->cat_ID;
		$title .= '<a href="' . get_term_feed_link( $term_id, $taxonomy_short_name, '' ) . '">' . __( 'RSS feed for this section','woothemes' ) . '</a></span>';

		$has_title = true;
	}

	// Day Archive
	if ( is_day() ) {

		$title = __( 'Archive', 'woothemes' ) . $delimiter . get_the_time( $date_format );
	}

	// Month Archive
	if ( is_month() ) {

		$date_format = apply_filters( 'woo_archive_title_date_format', 'F, Y' );
		$title = __( 'Archive', 'woothemes' ) . $delimiter . get_the_time( $date_format );
	}

	// Year Archive
	if ( is_year() ) {

		$date_format = apply_filters( 'woo_archive_title_date_format', 'Y' );
		$title = __( 'Archive', 'woothemes' ) . $delimiter . get_the_time( $date_format );
	}

	// Author Archive
	if ( is_author() ) {

		$title = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
	}

	// Tag Archive
	if ( is_tag() ) {

		$title = __( 'Tag Archives', 'woothemes' ) . $delimiter . single_tag_title( '', false );
	}

	// Post Type Archive
	if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {

		/* Get the post type object. */
		$post_type_object = get_post_type_object( get_query_var( 'post_type' ) );

		$title = $post_type_object->labels->name;
                
                //add_filter( 'wpseo_title', 'change_yoast_title', 10, 1 );
	}

	// Post Format Archive
	if ( get_query_var( 'taxonomy' ) == 'post_format' ) {

		$post_format = str_replace( 'post-format-', '', get_query_var( 'post_format' ) );

		$title = get_post_format_string( $post_format ) . ' ' . __( ' Archives', 'woothemes' );
	}

	// General Taxonomy Archive
	if ( is_tax() ) {

		$title = sprintf( __( '%1$s Archives: %2$s', 'woothemes' ), $taxonomy_raw_obj->labels->name, $taxonomy_obj->name );

	}

	if ( strlen($title) == 0 )
	return;

	$title = $before . $title . $after;

	// Allow for external filters to manipulate the title value.
	$title = apply_filters( 'woo_archive_title', $title, $before, $after );

	if ( $echo )
		echo $title;
	else
		return $title;

} // End woo_archive_title()

function get_related_posts_by_taxonomy($post_id, $taxonomy, $post_type, $args=array()) {
    $query = new WP_Query();
    $terms = wp_get_object_terms( $post_id, $taxonomy );
    
    // Make sure we have terms from the current post
    if ( count( $terms ) ) {
        $post_ids = get_objects_in_term( $terms[0]->term_id, $taxonomy );
        
        $args = wp_parse_args( $args, array(
                'post_type' => $post_type,
                'post__in' => $post_ids,
                'taxonomy' => $taxonomy,
                'term' => $terms[0]->slug,
            ) );
        $query = new WP_Query( $args );
    }

    // Return our results in query form
    return $query;
}

function get_site_meta_url($site_id) {
    $site_array = array();
    $site_array['url'] = types_render_field('hemsida-url', array('output' => 'raw'));
    //$site_array['url'] = do_shortcode("[types id='$site_id']");
    
    $site_domain_parts = parse_url($site_array['url']);
    
    $site_array['pretty_url'] = $site_domain_parts['host'];;
    
    return $site_array;
}

function get_site_meta_fakta($site_id) {
    return types_render_field('fakta', array('output' => 'html'));
}

function pr($array, $title='Array') {
    echo('<p>' . $title . ':</p><pre>');
    print_r($array);
    echo('</pre>');
}