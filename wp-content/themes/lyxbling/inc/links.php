<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
        $post_id = $post->ID;
        
        if('rabattkoder-smycken' == $post->post_type) {
            if(true == $_GET['iframe']) {
                global $wp_query;
                $wp_query->is_404 = false;
                include(get_stylesheet_directory() . '/single-rabattkod-external.php');
                exit;
            }
        }
        
        $link_data = lb_get_link_data($post_id);
        
        $redirect_url = $link_data['final_target_url'];   //get_post_meta($post->ID, 'wpcf-target-url', true); //lb_get_post_meta($post->ID, 'target-url');
        //$redirect_url = get_post_meta($post->ID, 'wpcf-target-url', true); //lb_get_post_meta($post->ID, 'target-url');
/*echo("redirect_url=$redirect_url<br>");
pr($link_data);
exit;*/

        header("X-Robots-Tag: noindex, nofollow", true);
        header("Location: " . $redirect_url, true, 302);

        exit;
    }
    
    return $template;
}
add_action( 'template_redirect', 'lb_external_links_template' );

function lb_get_external_link_url($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    return '/till/' . $post_id;
}
/*
 * Get the link data.
 */
function lb_get_link_data($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    $link_data_array = array();
    $post_type = get_post_type($post_id);
    
    // Set the associated store post ID. If this is a store post, just set it to the current $post_id.
    if('smyckesbutiker' == $post_type) {
        $link_data_array['associated_store_id'] = $post_id;
    } else {
        $link_data_array['associated_store_id'] = lb_get_associated_store_id($post_id);
    }
    
    // Set the brand information, based on the associated store.
    $link_data_array['brand'] = lb_get_brand($link_data_array['associated_store_id']);
    
    $link_data_array['target_url'] = lb_get_target_url($post_id);
    $link_data_array['final_target_url'] = $link_data_array['target_url'];
    $link_data_array['non_affiliate_target_url'] = $link_data_array['target_url'];
    $link_data_array['display_url'] = lb_get_display_url($post_id);
    $link_data_array['pretty_url'] = lb_get_pretty_url($link_data_array['display_url']);
    
    $link_data_array['external'] = lb_is_external_url($link_data_array['final_target_url']);
    
    $link_data_array['button_text'] = 'Gå till ' . $link_data_array['brand'];
    
    // Get the affiliate link data for the store this post is associated with.
    $affiliate_data_array = lb_get_affiliate_data($post_id, $link_data_array);
    
    $link_data_array = array_merge($link_data_array, $affiliate_data_array);
    
    // Check for any affiliate URL and set the final target URL to that.
    if(isset($link_data_array['affiliate_url']))
        $link_data_array['final_target_url'] = $link_data_array['affiliate_url'];
    
    return $link_data_array;
}

function lb_get_affiliate_data($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    $affiliate_data_array = array();
    $non_affiliate_target_url = lb_get_target_url($post_id);
    $post_type = get_post_type($post_id);
    
    $associated_store_id = lb_get_associated_store_id($post_id);
    // Only return affiliate data if there is a associated store, otherwise return an empty array.
    if($associated_store_id) {
        $butik_post_id = $associated_store_id;
    } else {
        return array();
    }
    
    // If there is an associated store post ID, we set the $post_id to that store post ID.
    if(isset($butik_post_id)) {
        $post_id = $butik_post_id;
        $affiliate_network = trim(get_post_meta($post_id, 'wpcf-affiliatenatverk', true));
    // If there's no associated store ID, we return an empty array. 
    } else {
        return array();
    }
    
    // If the store doesn't have any affiliate network associated with it we return an empty array.
    if('' == $affiliate_network || 'none' == $affiliate_network) {
        return array();
    } else {
        $affiliate_data_array['external'] = true;
        $program_id = trim(get_post_meta($post_id, 'wpcf-program-id', true));
        $site_id = trim(get_post_meta($post_id, 'wpcf-site-id', true));
        $ad_id = trim(get_post_meta($post_id, 'wpcf-ad-id', true));

        switch($affiliate_network) {
            case 'tradedoubler':
                $affiliate_data_array['affiliate_url'] = 'http://clk.tradedoubler.com/click?p=' . $program_id . '&a=' . $site_id . '&g=' . $ad_id . ('' != trim($non_affiliate_target_url)?'&url=' . $non_affiliate_target_url:'');
                // Problem with output when using javascript code below:
                //$impression_tracking_javascript = '<script type="text/javascript">var uri = \'http://impse.tradedoubler.com/imp?type(inv)g(' . $ad_id . ')a(' . $site_id . ')\' + new String (Math.random()).substring (2, 11);document.write(\'<img src="\'+uri +\'">\');</script>';
                $impression_tracking_javascript = '';
                $impression_tracking_image = '<img src="http://impse.tradedoubler.com/imp?type(inv)g(' . $ad_id . ')a(' . $site_id . ')" width="0" height="0" />';
                
                break;
            case 'adrecord':
                $affiliate_data_array['affiliate_url'] = 'http://click.adrecord.com/?p=' . $program_id . '&c=' . $site_id . ('' != trim($non_affiliate_target_url)?'&url=' . $non_affiliate_target_url:'');
                $impression_tracking_javascript = '';
                $impression_tracking_image = '';
                break;
            case 'double':
                //http://track.double.net/click/?channel=49931&ad=22883&epi=EPI&epi2=EPI2" style="background:url(http://track.double.net/display.gif?channel=49931&ad=22883&epi=EPI&epi2=EPI2) no-repeat;" target="_blank">Cocoo.se - Nordens st&#246;rsta n&#228;tbutik f&#246;r smycken</a>
                $affiliate_data_array['affiliate_url'] = 'http://track.double.net/click/?channel=' . $program_id . '&ad=' . $ad_id . ('' != trim($non_affiliate_target_url)?'&url=' . $non_affiliate_target_url:'');
                $impression_tracking_javascript = '';
                $impression_tracking_image = '<img src="http://track.double.net/display.gif?channel=' . $program_id . '&ad=' . $ad_id . '" width="0" height="0" />';
                break;
            case 'affiliator':
                $affiliate_data_array['affiliate_url'] = 'http://click.affiliator.com/click/a/' . $ad_id . '/b/0/w/' . $site_id . '/p/' . $program_id . '/' . ('' != trim($non_affiliate_target_url)?'direct_link/' . $non_affiliate_target_url:'');
                $impression_tracking_javascript = '';
                $impression_tracking_image = '<img src="http://imp.affiliator.com/imp.php?a=' . $ad_id . '&b=0&w=' . $site_id . '&p=' . $program_id . '" width="0" height="0" />';
                //http://imp.affiliator.com/imp.php?a=1159&b=8747&w=36046&p=276
                break;
            case 'adsettings':
                $affiliate_data_array['affiliate_url'] = 'http://www.adsettings.com/scripts/reg_click.php?aid=' . $ad_id . '&pid=' . $program_id . ('' != trim($non_affiliate_target_url)?'&url=' . $non_affiliate_target_url:'');
                $impression_tracking_javascript = '';
                $impression_tracking_image = '<img src="http://www.adsettings.com/scripts/gen_space_img.php?aid=' . $ad_id . '&pid=' . $program_id . '" width="0" height="0" />';
                //http://www.adsettings.com/scripts/reg_click.php?aid=2560&pid=23718
                //http://www.adsettings.com/scripts/gen_space_img.php?aid=2560&pid=23718
                break;
            case 'adtraction':
                $affiliate_data_array['affiliate_url'] = 'http://track.adtraction.com/t/t?a=' . $ad_id . '&as=' . $site_id . '&t=2&tk=1' . ('' != trim($non_affiliate_target_url)?'&url=' . $non_affiliate_target_url:'');
                $impression_tracking_javascript = '';   //<script type="text/javascript" src="http://track.adtraction.com/t/t?as=' . $site_id . '&t=1&tk=0&trt=2" charset="UTF-8"></script>';
                $impression_tracking_image = '';
                //http://track.adtraction.com/t/t?a=1010647960&as=1050030987&t=2&tk=1&url=http://www.frogpearl.se/
                //<script type="text/javascript" src="http://track.adtraction.com/t/t?as=1050030987&t=1&tk=0&trt=2" charset="ISO-8859-1"></script>
                break;
            case 'affilinet':
                break;
            case 'cj':
                break;
            case 'zanox':
                $affiliate_data_array['affiliate_url'] = 'http://ad.zanox.com/ppc/?' . $ad_id . ('' != trim($non_affiliate_target_url)?'&ulp=' . $non_affiliate_target_url:'');
                $impression_tracking_javascript = '';
                $impression_tracking_image = '';
                //http://ad.zanox.com/ppc/?27051148C2094246278&ulp=[[%2Faccessoarer-dam-smycken%2F]]
                break;
            default:
                $impression_tracking_javascript = '';
                $impression_tracking_image = '';
                break;
        }
        
        // Only show impression tracking image and javascript if not already shown on a page.
        global $is_impression_tracking_set;
        if($is_impression_tracking_set[$butik_post_id]) {
            $affiliate_data_array['affiliate_impression_tracking'] = '';
        } else {
            $affiliate_data_array['affiliate_impression_tracking'] = $impression_tracking_javascript . $impression_tracking_image;
            $is_impression_tracking_set[$butik_post_id] = true;
        }
        
    }
    
    return $affiliate_data_array;
}

/*
 * Get target URL.
 */
function lb_get_target_url($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    $target_url = trim(get_post_meta($post_id, 'wpcf-target-url', true));
    
    // If no final target URL exists, we link to the post permalink.
    if('' == $target_url || is_archive()) {
        $target_url = get_permalink($post_id);
        
        // If the permalink URL is the same as the current URL, we don't set a target URL.
        if(lb_get_current_page_url() == get_permalink($post_id)) {
            $target_url = '';
        }
    }
    
    if('http://lyxbling.se' == trim($target_url)) {
        $target_url = '';
    }
    
    return $target_url;
}

/*
 * Get display URL.
 */
function lb_get_display_url($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    return get_post_meta($post_id, 'wpcf-display-url', true);
}

/*
 * Get pretty URL.
 */
function lb_get_pretty_url($url) {
    $target_domain_parts = parse_url($url);
    
    return $target_domain_parts['host'];
}

/*
 * Get brand.
 */
function lb_get_brand($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    $brand = trim(get_post_meta($post_id, 'wpcf-varumarke', true));
    
    // If there's no brand data, get the associated store brand.
    if(empty($brand)) {
        $store_id = lb_get_associated_store_id($post_id);
        $brand = trim(get_post_meta($store_id, 'wpcf-varumarke', true));
    }
    
    return $brand;
}

/*
 * Get the link button.
 */
function lb_get_link_button($post_id = NULL, $align='left') {
    if(!$post_id)
        $post_id = get_the_ID();
    
    $post_type = get_post_type($post_id);
    $impression_tracking = '';
    $rel_external = '';
    
    $brand = lb_get_brand($post_id);
    $affiliate_data = lb_get_affiliate_data($post_id);
    $impression_tracking = $affiliate_data['affiliate_impression_tracking'];
    
    $target_url = lb_get_target_url($post_id);
    
    // If the target URL is empty, we just return an empty string.
    if(empty($target_url)) {
        return '';
    }
    
    $external_link = lb_is_external_url($target_url);
    if($external_link) {
        $target_url = lb_get_external_link_url();
    }
    
    $button_text = __( 'Continue Reading', 'woothemes' ) . ' &raquo;';
    
    switch($post_type) {
        case 'smyckesbutiker':
            $button_text = __( 'Gå till ', 'woothemes' ) . ' ' . $brand;
            $external_link = true;
            break;
        case 'smyckesvarumarken':
            $button_text = __( 'Gå till ', 'woothemes' ) . ' ' . $brand;
            $external_link = true;
            break;
        case 'smyckestavlingar':
            $button_text = __( 'Gå till tävlingen', 'woothemes' );
            break;
        case 'rabattkoder-smycken':
            $rabattkod = lb_get_rabattkod($post_id);
            $external_link = true;
            
            // If there's no rabattkod to be used, don't show the iframe.
            if('' == trim($rabattkod)) {
                $butik_post = lb_get_related_posts_by_taxonomy($post_id, 'butik', 'smyckesbutiker');
                $button_text = __( 'Gå till ', 'woothemes' ) . ' ' . lb_get_brand($butik_post->posts[0]->ID);
            } else {
                $target_url .= '?iframe=true';
                $button_text = __( 'Visa rabattkod', 'woothemes' );
            }
            break;
        case 'presenttips':
            $button_text = __( 'Gå till presenttips', 'woothemes' );
            break;
        case 'smyckeserbjudanden':
            $button_text = __( 'Gå till erbjudandet', 'woothemes' );
            break;
        default:
            $button_text = __( 'Continue Reading', 'woothemes' );
            break;
    }
    
    if(true == $external_link) {
        $rel_external = ' rel="external" ';
    }
    
    return '<div class="lb-button ' . $align . '" style="clear: both;" data-url="' . $target_url . '"' . $rel_external . '>' . $button_text . '</div>' . $impression_tracking . '<br clear="all" />';
}

function lb_get_link($post_id = NULL, $anchor_text = '', $external = true) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    $link_data = lb_get_link_data($post_id);
    
    $target_url = $link_data['target_url'];
    
    if(true == $link_data['external']) {
        $target_url = lb_get_external_link_url($post_id);
        $rel_external = ' rel="external" ';
    }
    
    $microdata = ' itemprop="url"';
    
    if('' == $anchor_text) {
        $anchor_text = $link_data['pretty_url'];
    }
    
    if(true == $external) {
        $rel_external = ' rel="external" ';
    }
    
    //return '<a href="' . $link_url . '"' . $microdata . $link_rel . ' target="_blank">' . $anchor_text . '</a>';
    return '<span class="lb-homepage-link" data-url="' . $target_url . '"' . $microdata . $rel_external . ' rel="external">' . $anchor_text . '</span>';
}

function lb_get_associated_store_id($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    $associated_store_post = lb_get_related_posts_by_taxonomy($post_id, 'butik', 'smyckesbutiker');
    
    // Return the associated store post ID if it exists, otherwise return false.
    if(isset($associated_store_post->posts[0]->ID)) {
        return $associated_store_post->posts[0]->ID;
    } else {
        return false;
    }
}

function lb_get_current_page_url() {
    $pageURL = 'http';
    
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    
    $pageURL .= "://";
    
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    
    return $pageURL;
}

/*
 * Get rabattkod.
 */
function lb_get_rabattkod($post_id) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    return get_post_meta($post_id, 'wpcf-rabattkod', true);
}

function lb_is_external_url($url) {
    $target_url_parts = parse_url($url);
    $current_url_parts = parse_url(lb_get_current_page_url());
    
    // Check if the final target URL is on another domain than this domain.
    if($target_url_parts['host'] != $current_url_parts['host']) {
        return true;
    } else {
        return false;
    }
}
?>
