<?php
 
// Load the textdomain for translation
load_child_theme_textdomain( 'woothemes' );

function lb_redirect_link() {
    /*
    $url_trigger = 'till';
    $request = $_SERVER['REQUEST_URI'];
    if (!isset($_SERVER['REQUEST_URI'])) {
            $request = substr($_SERVER['PHP_SELF'], 1);
            if (isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING'] != '') { $request.='?'.$_SERVER['QUERY_STRING']; }
    }
    if ( strpos('/'.$request, '/' . $url_trigger . '/') ) {
            $gocode_key = explode($url_trigger . '/', $request);
            $gocode_key = $gocode_key[1];
            $gocode_key = str_replace('/', '', $gocode_key);
            $table_name = $wpdb->prefix . "wsc_gocodes";
            $gocode_key = $wpdb->escape($gocode_key);
            $gocode_db = $wpdb->get_row("SELECT id, target, key1, docount FROM $table_name WHERE key1 = '$gocode_key'", OBJECT);
            $gocode_target = $gocode_db->target;
            if ($gocode_target!="") {
                    if ($gocode_db->docount == 1) {
                            $update = "UPDATE ". $table_name ." SET hitcount=hitcount+1 WHERE id='$gocode_db->id'";
                            $results = $wpdb->query( $update );
                    }
                    if ($nofollow != '') { header("X-Robots-Tag: noindex, nofollow", true); }
                    wp_redirect($gocode_target, 301);
                    exit;
            } else { $badgckey = get_option('siteurl'); wp_redirect($badgckey, 301); exit; }
    }
     * */
}
add_action('init', lb_redirect_link);

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

function lb_get_related_posts_by_taxonomy($post_id, $taxonomy, $post_type, $args=array()) {
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

function lb_get_post_meta($post_id, $custom_field, $args = array('output' => 'raw')) {
    return types_render_field("$custom_field", $args);
}

function lb_get_post_meta_homepage_url($post_id) {
    $homepage_array = array();

    $homepage_array['url'] = lb_get_post_meta($post_id, 'hemsida-url');
    $homepage_array['affiliate_url'] = lb_get_post_meta($post_id, 'affiliate-url');
    
    $homepage_domain_parts = parse_url($homepage_array['url']);
    
    $homepage_array['pretty_url'] = $homepage_domain_parts['host'];;

    return $homepage_array;
}

function lb_get_post_meta_homepage_link($post_id, $anchor_text = '') {
    $homepage_array = lb_get_post_meta_homepage_url($post_id);
    
    $link_url = $homepage_array['url'];
    $microdata = ' itemprop="url"';
    $link_rel = '';
    $anchor_text = $homepage_array['pretty_url'];
    
    if('' != trim($homepage_array['affiliate_url'])) {
        $link_url = $homepage_array['affiliate_url'];
        $link_rel = ' rel="nofollow"';
    }
    
    return '<a href="' . $link_url . '"' . $microdata . $link_rel . ' target="_blank">' . $anchor_text . '</a>';
}

function lb_get_post_meta_fakta($post_id) {
    if('' != trim(lb_get_post_meta($post_id, 'varumarke'))) {
        $brand = trim(lb_get_post_meta($post_id, 'varumarke'));
        $brand_microformats = '<span itemprop="brand">' . $brand . '</span>';
    }
    
    $fakta_html = '<div itemscope itemtype="http://schema.org/Organization"><h2>Fakta &amp; kontaktuppgifter för ' . $brand_microformats . '</h2>';
    
    if('' != trim(lb_get_post_meta($post_id, 'fakta-wysiwyg')))
        $fakta_html .= '<div id="fakta-fritext" itemprop="description">' . lb_get_post_meta($post_id, 'fakta-wysiwyg', $args = array('output' => 'html')) . '</div>';
    if('' != trim(lb_get_post_meta_homepage_link($post_id)))
        $fakta_html .= '<strong>Hemsida:</strong> ' . lb_get_post_meta_homepage_link($post_id) . '<br /><br />';
    if('' != trim(lb_get_post_meta($post_id, 'gatuadress'))) 
        $fakta_html .= '<strong>Adress:</strong><br /><section itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span class="fn org">' . lb_get_post_meta($post_id, 'foretagsnamn') . '</span><br /><span itemprop="streetAddress">' . lb_get_post_meta($post_id, 'gatuadress') . '</span><br /><span itemprop="postalCode">' . substr_replace(lb_get_post_meta($post_id, 'postnummer'), ' ', 3, 0) . '</span> <span itemprop="addressLocality">' . lb_get_post_meta($post_id, 'postadress') . '</span></section><br />';
    if('' != trim(lb_get_post_meta($post_id, 'telefon')))
        $fakta_html .= '<strong>Telefon:</strong> <span itemprop="telephone">' . lb_get_post_meta($post_id, 'telefon') . '</span><br /><br />';
    if('' != trim(lb_get_post_meta($post_id, 'e-post')))
        $fakta_html .= '<strong>E-post:</strong> <a href="mailto:' . lb_get_post_meta($post_id, 'e-post') . '" itemprop="email">' . lb_get_post_meta($post_id, 'e-post') . '</a><br /><br />';
    if('' != trim(lb_get_post_meta($post_id, 'organisationsnummer')))
        $fakta_html .= '<strong>Organisationsnummer:</strong> <span itemprop="taxID">' . lb_get_post_meta($post_id, 'organisationsnummer') . '</span><br /><br />';
    
    $fakta_html .= '</div><!- itemscope itemtype="http://schema.org/Organization" -->';
    
    return $fakta_html;
}

function lb_get_outgoing_competitions() {
    $todays_date = date('d.M.y');
    $todays_date_string = strtotime($todays_date);

    $args = array(
        'post_type' => 'tavlingar',
	'meta_key' => 'wpcf-slutdatum',
        'meta_compare' => '>=',
        'meta_value' => $todays_date_string,
        'posts_per_page' => '-1',
        'orderby' => 'wpcf-slutdatum',
        'order' => 'ASC'
    );
    
    $temp = $wp_query;
    $wp_query = null;
    $wp_query = new WP_Query();
    $wp_query->query($args);
    
    while ($wp_query->have_posts()) {
        $wp_query->the_post(); ?>
        <ul <?php post_class(); ?>>
            <li><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></li>
        </ul><?php
    }
}

function lb_get_social_buttons() { 
    $html_output = '<div class="social-buttons">';
    $html_output .= '<ul>';
    $html_output .= '<li class="iframetrack facebook"><div class="fb-like" data-href="" data-width="450" data-layout="button_count" data-show-faces="false" data-send="false"></div></li>';
    $html_output .= '<li class="pinterest"><a href="//pinterest.com/pin/create/button/" data-pin-do="buttonBookmark"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" alt="" /></a><script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script></li>';
    $html_output .= '<li class="iframetrack twitter"><a href="https://twitter.com/share" class="twitter-share-button" data-via="LyxBling" data-lang="sv" data-count="none">Tweeta</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script></li>';
    $html_output .= '<li class="iframetrack googleplus"><div class="g-plusone" data-size="medium" data-annotation="none"></div></li>';
    $html_output .= '<li class="linkedin"><script type="IN/Share"></script></li>';
    $html_output .= '</ul>';
    $html_output .= '</div>';
    
    return $html_output;
}

function lb_get_shop_social_buttons($post_id) { 
    $html_output = '<div class="social">';
    if('' != trim(lb_get_post_meta($post_id, 'facebook-url'))) 
        $html_output .= '<a target="_blank" href="' . lb_get_post_meta($post_id, 'facebook-url') . '" class="facebook" title="Facebook"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'twitter-url'))) 
        $html_output .= '<a target="_blank" href="' . lb_get_post_meta($post_id, 'twitter-url') . '" class="twitter" title="Twitter"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'instagram-url'))) 
        $html_output .= '<a target="_blank" href="' . lb_get_post_meta($post_id, 'instagram-url') . '" class="instagram" title="Instagram"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'pinterest-url'))) 
        $html_output .= '<a target="_blank" href="' . lb_get_post_meta($post_id, 'pinterest-url') . '" class="pinterest" title="Pinterest"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'youtube-url'))) 
        $html_output .= '<a target="_blank" href="' . lb_get_post_meta($post_id, 'youtube-url') . '" class="youtube" title="YouTube"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'google-url'))) 
        $html_output .= '<a target="_blank" href="' . lb_get_post_meta($post_id, 'google-url') . '" class="googleplus" title="Google+"></a>';
    
    $html_output .= '</div>';
    
    return $html_output;
}

function pr($array, $title='Array') {
    echo('<p>' . $title . ':</p><pre>');
    print_r($array);
    echo('</pre>');
}