<?php
/*
 * See canvas hooks and filters at http://woothemes.zendesk.com/entries/22533468-Canvas-Hook-Filter-Reference
 */

require_once('inc/overrides.php');
require_once('inc/links.php');
require_once('inc/store-information.php');
require_once('inc/rabattkoder.php');
require_once('inc/widgets.php');
require_once('inc/theme-options.php');

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
    if(function_exists('types_render_field'))
        return types_render_field($custom_field, $args);
    else
        return get_post_meta($post_id, 'wpcf_' . $custom_field, true);
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

function lb_get_social_share_buttons($array = false) { 
    // See http://www.l3analytics.com/2013/09/06/tracking-clicks-within-iframes-with-google-analytics-and-jquery/
    $html_output = '<aside id="social-share" class="social-share-buttons">';
    $html_output .= '<h3>Dela gärna denna sida med dina vänner</h3>';
    $html_output .= '<div class="social-buttons">';
    $html_output .= '<ul>';
    $html_output .= '<li class="iframetrack facebook"><div class="fb-like" data-href="" data-width="450" data-layout="button_count" data-show-faces="false" data-send="false"></div></li>';
    $html_output .= '<li class="pinterest"><a href="//pinterest.com/pin/create/button/" data-pin-do="buttonBookmark"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" alt="" /></a><script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script></li>';
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
function get_datetime_from_epoch($epoch, $format = 'Y-m-d') {
    date_default_timezone_set('Europe/Stockholm');
    $epoch = round($epoch/1000, 0);
    
    $dt = new DateTime("@$epoch"); // convert UNIX timestamp to PHP DateTime
    
    return $dt->format($format);
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
