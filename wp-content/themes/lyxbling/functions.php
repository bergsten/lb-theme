<?php
/*
 * See canvas hooks and filters at http://woothemes.zendesk.com/entries/22533468-Canvas-Hook-Filter-Reference
 */

require_once('inc/overrides.php');
require_once('inc/links.php');
require_once('inc/store-information.php');
require_once('inc/rabattkoder.php');
require_once('inc/product-feeds.php');
require_once('inc/widgets.php');
require_once('inc/theme-options.php');

/**
 * Include all custom javascript and CSS styles.
 */
function lb_enqueue_scripts() {
    wp_enqueue_style( 'lyxbling', get_stylesheet_directory_uri() . '/css/lyxbling.css' );
    wp_enqueue_script( 'lyxbling', get_stylesheet_directory_uri() . '/js/lyxbling.js', array('jquery'));
    wp_enqueue_script( 'pinterest', 'http://assets.pinterest.com/js/pinit.js');
    if(is_post_type_archive(array('smyckesbutiker', 'smyckesvarumarken', 'smyckesdesigners'))) {
        wp_enqueue_script( 'mixitup', get_stylesheet_directory_uri() . '/js/jquery.mixitup.min.js', array('jquery'));
    }
}
add_action( 'wp_enqueue_scripts', 'lb_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'lb_enqueue_scripts' );

/*
 * Load the textdomain for translation.
 */
function lb_child_theme_setup() {
    load_child_theme_textdomain( 'woothemes' );
}
add_action( 'after_setup_theme', 'lb_child_theme_setup' );

/*
 * Get all posts with a specific meta key/value pair.
 */
function lb_get_posts_with_meta_value($meta_key, $meta_value, $post_type = 'post', $post_status = 'publish', $meta_compare = '=') {
    $query = new WP_Query(
                array(
                    'post_type'     => $post_type,
                    'post_status'   => $post_status,
                    'meta_key'      => $meta_key,
                    'meta_value'    => $meta_value,
                    'meta_compare'  => $meta_compare,
                )
    );
    
    return $query;
}

// get taxonomies terms links
function lb_custom_taxonomies_terms_links($post_id) {
    // get post by post id
    $post = &get_post($post_id);
    // get post type by post
    $post_type = $post->post_type;
    // get post type taxonomies
    $taxonomies = get_object_taxonomies($post_type);
    $out = "<ul>";
    
    foreach ($taxonomies as $taxonomy) {        
        $out .= "<li>".$taxonomy.": ";
        // get the terms related to post
        $terms = get_the_terms( $post->ID, $taxonomy );
        if ( !empty( $terms ) ) {
            foreach ( $terms as $term )
                $out .= '<a href="' . get_term_link($term->slug, $taxonomy) .'">'.$term->name.'</a> ';
        }
        $out .= "</li>";
    }
    
    $out .= "</ul>";
    
    return $out;
} 

function lb_get_related_posts_by_taxonomy($post_id, $taxonomy, $post_type, $args=array()) {
    $terms = wp_get_object_terms($post_id, $taxonomy);
    // Make sure we have terms from the current post
    if(count($terms)) {
        $all_object_ids = array();
        
        foreach($terms as $term) {
            $object_ids = get_objects_in_term($term->term_id, $taxonomy);
            
            // Merge only the unique values in the arrays.
            $all_object_ids = array_merge(array_diff($all_object_ids, $object_ids), array_diff($object_ids, $all_object_ids));
        }
        
        $args = wp_parse_args($args, array(
                'post_type' => $post_type,
                'post__in' => $all_object_ids,
                'tax_query' => array(
                                    'taxonomy' => $taxonomy,
                                    'field' => 'id',
                                    'operator' => 'IN'
                                ),
            ) );

        $query = new WP_Query( $args );
    }

    // Return our results in query form
    return $query;
}

function lb_get_post_meta($post_id, $custom_field, $args = array('output' => 'raw')) {
    if(function_exists('types_render_field')) {
        $args = array_merge($args, array(
                    'id' => $post_id
                ));
        
        return types_render_field($custom_field, $args);
    } else {
        return get_post_meta($post_id, 'wpcf-' . $custom_field, true);
    }
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

function lb_get_social_share_buttons($array = false) { 
    // See http://www.l3analytics.com/2013/09/06/tracking-clicks-within-iframes-with-google-analytics-and-jquery/
    $html_output = '<aside id="social-share" class="social-share-buttons">';
    $html_output .= '<h3>Dela gärna denna sida med dina vänner</h3>';
    $html_output .= '<div class="social-buttons">';
    $html_output .= '<ul>';
    $html_output .= '<li class="iframetrack facebook"><div class="fb-like" data-href="" data-width="450" data-layout="button_count" data-show-faces="false" data-send="false"></div></li>';
    $html_output .= '<li class="pinterest"><a href="//pinterest.com/pin/create/button/" target="_blank" data-pin-do="buttonPin"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" alt="" /></a><script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script></li>';
    $html_output .= '<li class="iframetrack twitter"><a href="https://twitter.com/share" class="twitter-share-button" data-via="LyxBling" data-lang="sv" data-count="none">Tweeta</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script></li>';
    $html_output .= '<li class="iframetrack googleplus"><div class="g-plusone" data-size="medium" data-annotation="none"></div></li>';
    $html_output .= '<li class="linkedin"><script src="//platform.linkedin.com/in.js" type="text/javascript">lang: sv_SE</script><script type="IN/Share"></script></li>';
    $html_output .= '</ul>';
    $html_output .= '</div>';
    $html_output .= '</aside>';
    
    return $html_output;
}

/* 
 * See http://www.epochconverter.com/programming/functions-php.php
 */
function lb_get_datetime_from_epoch($epoch, $format = 'Y-m-d') {
    date_default_timezone_set('Europe/Stockholm');
    $epoch = round($epoch/1000, 0);
    
    $dt = new DateTime("@$epoch"); // convert UNIX timestamp to PHP DateTime
    
    return $dt->format($format);
}

/**
 * trims text to a space then adds ellipses if desired
 * @param string $input text to trim
 * @param int $length in characters to trim to
 * @param bool $ellipses if ellipses (...) are to be added
 * @param bool $strip_html if html tags are to be stripped
 * @return string 
 */
function lb_trim_text($input, $length, $ellipses = true, $strip_html = true) {
    //strip tags, if desired
    if ($strip_html) {
        $input = strip_tags($input);
    }
  
    //no need to trim, already shorter than trim length
    if (strlen($input) <= $length) {
        return $input;
    }
  
    //find last space within length
    $last_space = strrpos(substr($input, 0, $length), ' ');
    $trimmed_text = substr($input, 0, $last_space);
  
    //add ellipses (...)
    if ($ellipses) {
        $trimmed_text .= '...';
    }
  
    return $trimmed_text;
}

function lb_get_wp_base() {
    return '/home/lyxbling/public_html/';
}

function pr($array, $title='Array') {
    //if(is_admin()) {
        echo('<p>' . $title . ':</p><pre>');
        print_r($array);
        echo('</pre>');
    //}
}
