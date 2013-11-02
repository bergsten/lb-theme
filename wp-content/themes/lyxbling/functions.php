<?php
/*
 * See canvas hooks and filters at http://woothemes.zendesk.com/entries/22533468-Canvas-Hook-Filter-Reference
 */

/**
 * Include all custom javascript and CSS styles.
 */
function lb_enqueue_scripts() {
        wp_enqueue_style( 'lyxbling', get_stylesheet_directory_uri() . '/css/lyxbling.css' );
        wp_enqueue_script( 'lyxbling', get_stylesheet_directory_uri() . '/js/lyxbling.js', array('jquery'));
        wp_enqueue_script( 'pinterest', 'http://assets.pinterest.com/js/pinit.js');
}
add_action( 'wp_enqueue_scripts', 'lb_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'lb_enqueue_scripts' );

/*
 * Remove '?ver' from all .js and .css files in header.
 */
function _remove_script_version( $src ){
    $parts = explode( '?ver', $src );
        return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

function new_excerpt_more( $more ) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

// Load the textdomain for translation.
add_action( 'after_setup_theme', 'lb_child_theme_setup' );
function lb_child_theme_setup() {
    load_child_theme_textdomain( 'woothemes' );
}

function lb_external_links_rewrite_rule() {
    add_rewrite_tag( '%postid%', '(\d+)', 'postid='); 
    add_rewrite_rule('till/(\d+)/?', 'index.php?pagename=till&postid=$matches[1]', 'top');
}
add_action('init', 'lb_external_links_rewrite_rule');  

function lb_register_query_vars( $vars ) {
    $vars[] = 'postid';
    $vars[] = 'pagename';
 
    return $vars;
}
add_filter( 'query_vars', 'lb_register_query_vars' );

function lb_external_links_template($template) {
    if(get_query_var('postid')) {
        $post = get_post(get_query_var('postid'));
        if('rabattkoder-smycken' == $post->post_type && true != $_GET['noiframe']) {
            global $wp_query;
            $wp_query->is_404 = false;
            include(get_stylesheet_directory() . '/single-rabattkod-external.php');
            exit;
        } else {
            $redirect_url = get_post_meta($post->ID, 'wpcf-target-url', true); //lb_get_post_meta($post->ID, 'target-url');
            header("X-Robots-Tag: noindex, nofollow", true);
            header("Location: " . $redirect_url, true, 301);
            exit;
        }
    }
    
    return $template;
}
add_action( 'template_redirect', 'lb_external_links_template' );

/*
 *  Create the custom taxonomy permalinks in the format /presenttips/%tips%/%postname% - See http://wp-types.com/forums/topic/custom-taxonomies-not-showing-on-permalink/#post-16949
 */
add_filter('post_link', 'lb_presenttips_permalink', 1, 3);
add_filter('post_type_link', 'lb_presenttips_permalink', 1, 3);
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

/*
 * Make the Yoast breadcrumbs do the same.
 */
function lb_adjust_single_breadcrumb( $link_output, $link ) {
        if (strpos($link['url'], '%tips%') === FALSE) return $link_output;
pr($link_output);
pr($link);
        $post = get_post();
        // Get taxonomy terms
        $terms = wp_get_object_terms($post->ID, 'tips');
pr($terms);
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
/* Custom breadcrumbs section under the navigation bar. */
/*-----------------------------------------------------------------------------------*/
function lb_woo_subheader() {
    if ( !is_front_page() ) { 
        if(file_exists(get_stylesheet_directory() . '/images/subheader-images/' . get_post_type(get_the_ID()) . '.png')) { ?>
            <style>
                #nav-container {
                    margin-bottom: 0 !important;
                    margin: 0 !important;
                }
            </style>
            <div id="subheader-image-container">
                <div id="subheader-image" style="background-image: url(<?php echo(get_stylesheet_directory_uri() . '/images/subheader-images/' . get_post_type(get_the_ID()) . '.png'); ?>) !important;">
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
// Remove logo code from Canvas - See https://gist.github.com/srikat/5581777
add_action('wp_head', 'remove_woo_logo');
function remove_woo_logo() {
    remove_action('woo_header_inside','woo_logo');
}
add_action( 'woo_header_inside', 'lb_woo_logo', 10 );

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
    // If a post ID is supplied we use the shortcode version that supports post ID.
    /*if('' != trim($post_id)) {
        $args_string = '';
        
        foreach ($args as $arg => $val) {
            $args_string .= ' ' . $arg . '="' . $val . '"';
        }
//echo('[types field="' . $custom_field . '" id="' . $post_id . '"' .  $args_string . ']');
echo(do_shortcode('[types field="' . $custom_field . '" id="' . $post_id . $args_string . ']'));
        return do_shortcode('[types field="' . $custom_field . '" id="' . $post_id . $args_string . ']');
    } else {
        return types_render_field("$custom_field", $args);
    }*/
}

function lb_aff_redirect_query($post_id) {
	global $wpdb, $table_prefix;
	$request = $_SERVER['REQUEST_URI'];
	if (!isset($_SERVER['REQUEST_URI'])) {
		$request = substr($_SERVER['PHP_SELF'], 1);
		if (isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING'] != '') { $request.='?'.$_SERVER['QUERY_STRING']; }
	}
	if (isset($_GET['gocode'])) {
		$request = '/go/'.$_GET['gocode'].'/';
	}
	$url_trigger = get_option("wsc_gocodes_url_trigger");
	$nofollow = get_option("wsc_gocodes_nofollow");
	if ($url_trigger=='') {
		$url_trigger = 'go';
	}
	if ( strpos('/'.$request, '/'.$url_trigger.'/') ) {
		$gocode_key = explode($url_trigger.'/', $request);
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
}
//***** End Redirection *****

function lb_get_link_button($post_id, $align='left') {
    $post_type = get_post_type($post_id);
    $brand = trim(lb_get_post_meta($post_id, 'varumarke'));
    $target_url = lb_get_post_meta($post_id, 'target-url');
    
    // If no target url is set we link to the post permalink.
    if('' == trim($target_url))
        $target_url = get_permalink($post->ID);
    
    $target_url_parts = parse_url($target_url);
    $current_url_parts = parse_url(lb_get_current_page_url());
    $rel_external = '';
    
    if($target_url_parts['host'] != $current_url_parts['host'])
        $rel_external = ' rel="external" ';
    
    $button_text = 'Gå till ' . $brand;
    
    switch($post_type) {
        case 'smyckesbutiker':
            break;
        case 'smyckesvarumarken':
            break;
        case 'smyckestavlingar':
            $button_text = __( 'Gå till tävlingen', 'woothemes' );
            $target_url = 'http://lyxbling.se/till/' . $post_id;
            break;
        case 'rabattkoder-smycken':
            $button_text = __( 'Visa rabattkod', 'woothemes' );
            $target_url = 'http://lyxbling.se/till/' . $post_id;
            break;
        case 'presenttips':
            $button_text = __( 'Gå till present', 'woothemes' );
            $target_url = 'http://lyxbling.se/till/' . $post_id;
            break;
        default:
            $button_text = __( 'Continue Reading', 'woothemes' );
            break;
    }
    
    $button_text .= ' &raquo;';
    
    
    
    return '<div class="lb-button ' . $align . '" style="clear: both;" data-url="' . $target_url . '"' . $rel_external . '>' . $button_text . '</div><br clear="all" />';
}

function lb_get_post_meta_target_url($post_id) {
    $target_url_array = array();
    
    $target_url_array['target_url'] = lb_get_post_meta($post_id, 'target-url');
    $target_url_array['display_url'] = lb_get_post_meta($post_id, 'display-url');
    
    $target_domain_parts = parse_url($target_url_array['display_url']);
    
    $target_url_array['pretty_url'] = $target_domain_parts['host'];;

    return $target_url_array;
}

function lb_get_post_meta_target_link($post_id, $anchor_text = '') {
    $target_url_array = lb_get_post_meta_target_url($post_id);
    
    $link_url = $target_url_array['target_url'];
    $microdata = ' itemprop="url"';
    $link_rel = '';
    $anchor_text = $target_url_array['pretty_url'];
    
    //return '<a href="' . $link_url . '"' . $microdata . $link_rel . ' target="_blank">' . $anchor_text . '</a>';
    return '<span class="lb-homepage-link" data-url="' . $link_url . '"' . $microdata . $link_rel . ' rel="external">' . $anchor_text . '</span>';
}

function lb_get_post_meta_fakta($post_id) {
    if('' != trim(lb_get_post_meta($post_id, 'varumarke'))) {
        $brand = trim(lb_get_post_meta($post_id, 'varumarke'));
        $brand_microformats = '<span itemprop="brand">' . $brand . '</span>';
    }
    
    $fakta_html = '<div itemscope itemtype="http://schema.org/Organization"><h2>Fakta &amp; kontaktuppgifter för ' . $brand_microformats . '</h2>';
    
    if('' != trim(lb_get_post_meta($post_id, 'fakta-wysiwyg')))
        $fakta_html .= '<div id="fakta-fritext" itemprop="description">' . lb_get_post_meta($post_id, 'fakta-wysiwyg', $args = array('output' => 'html')) . '</div>';
    if('' != trim(lb_get_post_meta_target_link($post_id)))
        $fakta_html .= '<strong>Hemsida:</strong> ' . lb_get_post_meta_target_link($post_id) . '<br /><br />';
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
        'post_status' => 'publish',
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

function lb_get_brands_sold($post_id) {
    $post = get_post($post_id);
//echo("postname: $post->post_name<br>");
    
    $brand_terms = get_the_terms($post->ID, 'varumarke');
    
    foreach($brand_terms as $brand_term) {
//pr($brand_term);
        $args = array(
                'post_type' => 'varumarken',
                'varumarke' => $brand_term,
        );
        $query = new WP_Query( $args );
//pr($query->posts, 'Query');
        $brands_args = get_posts( array(
            'post_type' => 'varumarken',
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'varumarke',
                    'field' => 'id',
                    'terms' => array($brand_term->term_taxonomy_id)
                )
            )
        ));
        $brands_query = new WP_Query($brands_args);
//pr($brands_query, 'Items');
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

function lb_get_current_page_url() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
     $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
     $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function lb_get_wp_base() {
    return '/home/lyxbling/public_html/';
}

function pr($array, $title='Array') {
    if(is_admin()) {
        echo('<p>' . $title . ':</p><pre>');
        print_r($array);
        echo('</pre>');
    }
}
