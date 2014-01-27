<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Remove '?ver' from all .js and .css files in header.
 */
function _remove_script_version( $src ){
    $parts = explode( '?ver', $src );
        return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

// Fix the top space issue for absolute positioned elements.
function lb_my_filter_head() {
    if (!is_user_logged_in())
	remove_action('wp_head', '_admin_bar_bump_cb');
}
add_action('get_header', 'lb_my_filter_head');

function lb_new_excerpt_more( $more ) {
	return '...';
}
add_filter('excerpt_more', 'lb_new_excerpt_more');

/*
 * Add all custom post type feeds to main feed.
 */
function lb_feed_request($qv) {
	if (isset($qv['feed']) && !isset($qv['post_type']))
		$qv['post_type'] = array('post', 'smyckesbutiker', 'smyckesvarumarken', 'smyckesdesigners', 'smyckesguider', 'smyckestavlingar', 'rabattkoder');
	return $qv;
}
add_filter('request', 'lb_feed_request');

/*-----------------------------------------------------------------------------------*/
/* Optional Top Navigation with Ajaxy Search (WP Menus) */
/* Also check out http://wp-ajaxsearchpro-admin.demo.wp-dreams.com/wp-admin/admin.php?page=ajax-search-pro/backend/settings.php&asp_sid=248  */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_top_navigation' ) ) {
    function woo_top_navigation() {
        if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) { ?>
        <div id="top">
            <div class="col-full"><?php
                echo '<h3 class="top-menu">' . woo_get_menu_name( 'top-menu' ) . '</h3>';
                wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav top-navigation fl', 'theme_location' => 'top-menu' ) ); ?>
                <div class="sf_container">
                    <div class="sf_search" style="border:1px solid #eee">
                        <span class="sf_block">
                            <input style="width:180px;" class="sf_input sf_focused" autocomplete="off" type="text" value="Sök" name="s"><button class="sf_button searchsubmit" type="submit"><span class="sf_hidden">Sök på LyxBling.se</span></button>
                        </span>
                    </div>
                </div>
            </div>
        </div><!-- /#top --><?php
        }
    } // End woo_top_navigation()
}

/*
 *  Create the custom taxonomy permalinks in the format /presenttips/%tips%/%postname% - See http://wp-types.com/forums/topic/custom-taxonomies-not-showing-on-permalink/#post-16949
 */
function lb_presenttips_permalink($permalink, $post_id, $leavename) {
        if (strpos($permalink, '%tips%') === FALSE) return $permalink;
 
        // Get post
        $post = get_post($post_id);
        if (!$post) return $permalink;
 
        // Get taxonomy terms
        $terms = wp_get_object_terms($post->ID, 'tips');
        if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) $taxonomy_slug = $terms[0]->slug;
        else $taxonomy_slug = 'diverse';
 
        return str_replace('%tips%', $taxonomy_slug, $permalink);
}
add_filter('post_link', 'lb_presenttips_permalink', 1, 3);
add_filter('post_type_link', 'lb_presenttips_permalink', 1, 3);

/*
 * Make the Yoast breadcrumbs do the same.
 */
function lb_adjust_single_breadcrumb( $link_output, $link ) {
        if (strpos($link['url'], '%tips%') === FALSE) return $link_output;
//pr($link_output);
//pr($link);
        $post = get_post();
        // Get taxonomy terms
        $terms = wp_get_object_terms($post->ID, 'tips');
//pr($terms);
        if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) $taxonomy_slug = $terms[0]->slug;
        else $taxonomy_slug = 'diverse';
        
        if(!empty($link[0])) {
            $link_output = str_replace('%tips%', $taxonomy_slug, $link_output);
        } else {
            $link_output = str_replace('/%tips%', '', $link_output);
            
        }
        
        return $link_output;
}
//add_filter('wpseo_breadcrumb_single_link_with_sep', 'lb_adjust_single_breadcrumb', 10, 2 );


/*-----------------------------------------------------------------------------------*/
/* Custom subheader image under the navigation bar.                                     */
/*-----------------------------------------------------------------------------------*/
function lb_woo_subheader() {
    if ( !is_front_page() ) {
        $post_id = get_the_ID();
        if('' != trim(lb_get_post_meta($post_id, 'sidhuvudbild'))) {
            $image_url = lb_get_post_meta($post_id, 'sidhuvudbild');
        } else if (file_exists(get_stylesheet_directory() . '/images/subheader-images/' . get_post_type(get_the_ID()) . '.jpg')) { 
            $image_url = get_stylesheet_directory() . '/images/subheader-images/' . get_post_type(get_the_ID()) . '.jpg';
        }
        
        if('' != trim($image_url)) {
            ?>
            <style>
                #nav-container {
                    margin-bottom: 0 !important;
                    margin: 0 !important;
                }
            </style>
            <div id="subheader-image-container">
                <div id="subheader-image" style="background-image: url(<?php echo($image_url); ?>) !important;">
                    <h1 id="subheader-text"><?php echo(lb_get_post_meta($post_id, 'varumarke')); ?></h1>
                </div>
            </div><?php
        }
    }
}
add_action( 'woo_content_before', 'lb_woo_subheader' );

/*-----------------------------------------------------------------------------------*/
/* Custom breadcrumbs section under the navigation bar. */
/*-----------------------------------------------------------------------------------*/
function lb_woo_breadcrumbs_section() {
    if ( !is_front_page() && function_exists('yoast_breadcrumb') ) { ?>
        <div id="breadcrumbs-container"><?php  
            yoast_breadcrumb('<p id="breadcrumbs">','</p></br>'); ?>
        </div><?php
    }
}
add_action( 'woo_main_before', 'lb_woo_breadcrumbs_section' );

/*-----------------------------------------------------------------------------------*/
/* Optionally load custom logo with microtags. */
/*-----------------------------------------------------------------------------------*/
function lb_woo_logo () {
	$settings = woo_get_dynamic_values( array( 'logo' => '' ) );
	// Setup the tag to be used for the header area (`h1` on the front page and `span` on all others).
	$heading_tag = 'span';
	if ( is_home() || is_front_page() ) { $heading_tag = 'h1'; }

	// Get our website's name, description and URL. We use them several times below so lets get them once.
	$site_title = get_bloginfo( 'name' );
	$site_url = home_url( '/' );
	$site_description = get_bloginfo( 'description' );
?>
<div id="logo" itemscope itemtype="http://schema.org/Organization">
<?php
	// Website heading/logo and description text.
	if ( ( '' != $settings['logo'] ) ) {
		$logo_url = $settings['logo'];
		if ( is_ssl() ) $logo_url = str_replace( 'http://', 'https://', $logo_url );

		echo '<a href="' . esc_url( $site_url ) . '" title="' . esc_attr( $site_description ) . '"><img itemprop="logo" src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $site_title ) . '" /></a>' . "\n";
	} // End IF Statement

	echo '<' . $heading_tag . ' class="site-title"><a itemprop="url" href="' . esc_url( $site_url ) . '"><span itemprop="brand">' . $site_title . '</span></a></' . $heading_tag . '>' . "\n";
	if ( $site_description ) { echo '<span itemprop="description" class="site-description">' . $site_description . '</span>' . "\n"; }
?>
</div>
<?php
} // End woo_logo()
add_action( 'woo_header_inside', 'lb_woo_logo', 10 );

// Remove logo code from Canvas - See https://gist.github.com/srikat/5581777
function lb_remove_woo_logo() {
    remove_action('woo_header_inside','woo_logo');
}
add_action('wp_head', 'lb_remove_woo_logo');

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
                    <h4><a href="<?php echo(get_the_author_meta( 'user_url', $author_id )); ?>">Skrivet av <span class="vcard author"><span class="fn"><?php echo(get_the_author_meta( 'display_name', $author_id )); ?></span></spand></a></h4>
                    <div class="date-updated">Sidan senast uppdaterad <span class="date updated"><?php echo(get_the_modified_date()); ?></span></div>
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

function lb_change_yoast_title($yoast_title) {
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

?>
