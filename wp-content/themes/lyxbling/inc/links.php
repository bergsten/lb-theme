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
        
        if('rabattkoder-smycken' == $post->post_type) {
            if(true != $_GET['noiframe']) {
                global $wp_query;
                $wp_query->is_404 = false;
                include(get_stylesheet_directory() . '/single-rabattkod-external.php');
                exit;
            } else {
                $butik_post = lb_get_related_posts_by_taxonomy($post->ID, 'butik', 'smyckesbutiker');
                $link_data = lb_get_link_data($butik_post->posts[0]->ID);
                
                if(isset($link_data['affiliate_url']))
                    $target_url = $link_data['affiliate_url']; 
                else
                    $target_url = $link_data['target_url'];
                
                $redirect_url = $target_url;   //get_post_meta($post->ID, 'wpcf-target-url', true); //lb_get_post_meta($post->ID, 'target-url');
            }
        } else {
            $redirect_url = get_post_meta($post->ID, 'wpcf-target-url', true); //lb_get_post_meta($post->ID, 'target-url');
        }
        
        header("X-Robots-Tag: noindex, nofollow", true);
        header("Location: " . $redirect_url, true, 301);

        exit;
    }
    
    return $template;
}
add_action( 'template_redirect', 'lb_external_links_template' );

/*
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
 * 
 */
//***** End Redirection *****

/*
 * Get the link data.
 */
function lb_get_link_data($post_id) {
    $link_data_array = array();
    $post_type = get_post_type($post_id);
    $link_data_array['brand'] = trim(get_post_meta($post_id, 'wpcf-varumarke', true));
    $link_data_array['target_url'] = get_post_meta($post_id, 'wpcf-target-url', true);
    $link_data_array['display_url'] = get_post_meta($post_id, 'wpcf-display-url', true);
    $target_domain_parts = parse_url($link_data_array['display_url']);
    $link_data_array['pretty_url'] = $target_domain_parts['host'];
    
    // If no target url is set we link to the post permalink.
    if('' == trim($link_data_array['target_url']))
        $link_data_array['target_url'] = get_permalink($post_id);
    // If http://lyxbling.se is set as target url we shouldn't set the target_url.
    if('http://lyxbling.se' == trim($link_data_array['target_url'])) {
        $link_data_array['target_url'] = '';
        
        return $link_data_array;
    }
    
    $target_url_parts = parse_url($link_data_array['target_url']);
    $current_url_parts = parse_url(lb_get_current_page_url());
    $link_data_array['external'] = false;
    
    if($target_url_parts['host'] != $current_url_parts['host'])
        $link_data_array['external'] = true;
    
    $link_data_array['button_text'] = 'Gå till ' . $link_data_array['brand'];
    
    $affiliate_data_array = lb_get_affiliate_data($post_id, $link_data_array);

    $link_data_array = array_merge($link_data_array, $affiliate_data_array);
    
    switch($post_type) {
        case 'smyckesbutiker':
            break;
        case 'smyckesvarumarken':
            break;
        case 'smyckestavlingar':
            $link_data_array['button_text'] = __( 'Gå till tävlingen', 'woothemes' );
            $link_data_array['target_url'] = '/till/' . $post_id;
            break;
        case 'rabattkoder-smycken':
            $rabattkod = get_post_meta($post_id, 'wpcf-rabattkod', true);
            $link_data_array['external'] = true;
            
            if('' == trim($rabattkod)) {
                $link_data_array['target_url'] = '/till/' . $post_id . '?noiframe=true';
                $butik_post = lb_get_related_posts_by_taxonomy($post_id, 'butik', 'smyckesbutiker');
                $link_data_array['button_text'] = __( 'Gå till ', 'woothemes' ) . ' ' . trim(get_post_meta($butik_post->posts[0]->ID, 'wpcf-varumarke', true));
            } else {
                $link_data_array['target_url'] = '/till/' . $post_id;
                $link_data_array['button_text'] = __( 'Visa rabattkod', 'woothemes' );
            }
            break;
        case 'presenttips':
            $link_data_array['external'] = true;
            $link_data_array['button_text'] = __( 'Gå till presenttips', 'woothemes' );
            $link_data_array['target_url'] = '/till/' . $post_id;
            break;
        default:
            $link_data_array['button_text'] = __( 'Continue Reading &raquo;', 'woothemes' );
            $link_data_array['target_url'] = get_permalink($post_id);
            $link_data_array['external'] = false;
            break;
    }

    return $link_data_array;
}

function lb_get_affiliate_data($post_id, $link_data_array = array()) {
    $affiliate_data_array = array();
    $affiliate_network = trim(get_post_meta($post_id, 'wpcf-affiliatenatverk', true));
            
    if('' == $affiliate_network || 'none' == $affiliate_network) {

    } else {
        $affiliate_data_array['external'] = true;
        $program_id = trim(get_post_meta($post_id, 'wpcf-program-id', true));
        $site_id = trim(get_post_meta($post_id, 'wpcf-site-id', true));
        $ad_id = trim(get_post_meta($post_id, 'wpcf-ad-id', true));

        switch($affiliate_network) {
            case 'tradedoubler':
                $affiliate_data_array['affiliate_url'] = 'http://clk.tradedoubler.com/click?p=' . $program_id . '&a=' . $site_id . '&g=' . $ad_id . '&url=' . $link_data_array['target_url'];
                $impression_tracking_javascript = '<script type="text/javascript">var uri = \'http://impse.tradedoubler.com/imp?type(inv)g(' . $ad_id . ')a(' . $site_id . ')\' + new String (Math.random()).substring (2, 11);document.write(\'<img src="\'+uri +\'">\');</script>';
                $impression_tracking_image = '<img src="http://impse.tradedoubler.com/imp?type(inv)g(' . $ad_id . ')a(' . $site_id . ')">';
                $affiliate_data_array['affiliate_impression_tracking'] = $impression_tracking_javascript . $impression_tracking_image;
                break;
            case 'adrecord':
                $affiliate_data_array['affiliate_url'] = 'http://click.adrecord.com/?p=' . $program_id . '&c=' . $site_id . '&url=' . $link_data_array['target_url'];
                break;
            case 'double':
                //http://track.double.net/click/?channel=49931&ad=22883&epi=EPI&epi2=EPI2" style="background:url(http://track.double.net/display.gif?channel=49931&ad=22883&epi=EPI&epi2=EPI2) no-repeat;" target="_blank">Cocoo.se - Nordens st&#246;rsta n&#228;tbutik f&#246;r smycken</a>
                $affiliate_data_array['affiliate_url'] = 'http://track.double.net/click/?channel=' . $program_id . '&ad=' . $ad_id . '&url=' . $link_data_array['target_url'];
                $affiliate_data_array['affiliate_impression_tracking'] = '<img src="http://track.double.net/display.gif?channel=' . $program_id . '&ad=' . $ad_id . '">';
                break;
            case 'affiliator':
                $affiliate_data_array['affiliate_url'] = 'http://click.affiliator.com/click/a/' . $ad_id . '/b/0/w/' . $site_id . '/p/' . $program_id . '/direct_link/' . $link_data_array['target_url'];
                $affiliate_data_array['affiliate_impression_tracking'] = '<img src="http://imp.affiliator.com/imp.php?a=' . $ad_id . '&b=0&w=' . $site_id . '&p=' . $program_id . '" width="0" height="0" />';
                //http://imp.affiliator.com/imp.php?a=1159&b=8747&w=36046&p=276
                break;
            case 'adtraction':
                break;
            case 'affilinet':
                break;
            case 'cj':
                break;
            case 'zanox':
                break;
            default:
                break;
        }
    }
    
    return $affiliate_data_array;
}

function lb_get_target_url($post_id) {
    $link_data = lb_get_link_data($post_id);
    
    $target_url = $link_data['target_url'];
    
    if(isset($link_data['affiliate_url']))
        $target_url = $link_data['affiliate_url'];
    
    return $target_url;
}

/*
 * Get the link button.
 */
function lb_get_link_button($post_id, $align='left') {
    $link_data = lb_get_link_data($post_id);
    $impression_tracking = '';
    $rel_external = '';
    $target_url = $link_data['target_url'];
    
    if('' == trim($target_url))
        return '';
    if(isset($link_data['affiliate_url']))
        $target_url = $link_data['affiliate_url'];
    if(true == $link_data['external'])
        $rel_external = ' rel="external" ';
    if(isset($link_data['affiliate_impression_tracking']))
        $impression_tracking = $link_data['affiliate_impression_tracking'];
    
    $button_text = $link_data['button_text'] . ' &raquo;';
    
    return '<div class="lb-button ' . $align . '" style="clear: both;" data-url="' . $target_url . '"' . $rel_external . '>' . $button_text . '</div>' . $impression_tracking . '<br clear="all" />';
}

function lb_get_link($post_id, $anchor_text = '', $external = true) {
    $link_data = lb_get_link_data($post_id);
    
    $target_url = $link_data['target_url'];
    
    if(isset($link_data['affiliate_url']))
        $target_url = $link_data['affiliate_url'];

    if(true == $link_data['external'])
        $rel_external = ' rel="external" ';

    $microdata = ' itemprop="url"';
    
    if('' == $anchor_text)
        $anchor_text = $link_data['pretty_url'];
    
    if(true == $external)
        $rel_external = ' rel="external" ';
    
    //return '<a href="' . $link_url . '"' . $microdata . $link_rel . ' target="_blank">' . $anchor_text . '</a>';
    return '<span class="lb-homepage-link" data-url="' . $target_url . '"' . $microdata . $rel_external . ' rel="external">' . $anchor_text . '</span>';
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
?>
