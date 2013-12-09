<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function lb_rabattkoder_setup_schedule() {
	if ( ! wp_next_scheduled( 'lb_rabattkoder_daily_event' ) ) {
		wp_schedule_event( time(), 'daily', 'lb_rabattkoder_daily_event');
	}
}
add_action( 'wp', 'lb_rabattkoder_setup_schedule' );

/**
 * On the scheduled action hook, run a function.
 */
function lb_rabattkoder_do_daily() {
    //$result = lb_update_rabattkoder('tradedoubler');
    $result = lb_update_rabattkoder('double');
}
//add_action('init', 'lb_rabattkoder_do_daily');
//add_action( 'lb_rabattkoder_daily_event', 'lb_rabattkoder_do_daily' );

function lb_update_rabattkoder($affiliate_network) {
    $result = array('result' => false);
    
    switch($affiliate_network) {
        case 'tradedoubler':
            // https://api.tradedoubler.com/1.0/vouchers.json;siteSpecific=false?token=FF47AC9656810C3F795E2407E9B67828D1B7561F
            $voucher_key = 'FF47AC9656810C3F795E2407E9B67828D1B7561F';
            $api_url = 'https://api.tradedoubler.com/1.0/vouchers.json;siteSpecific=false?token=' . $voucher_key;
            $args = array('sslverify' => false);
            break;
        case 'double':
            // https://www.double.net/api/publisher/v2/coupons/?program=826&format=json
            $api_token = 'a346cb979e3395f33c87585d15a576a6a2cb8de2';
            $api_url = 'https://www.double.net/api/publisher/v2/coupons/?format=json';
            $args = array('sslverify' => false, 'headers' => array('Authorization' => 'Token ' . $api_token));
            break;
        default:
            break;
    }
    
    $api_response = wp_remote_retrieve_body(wp_remote_get($api_url, $args));
    
    if(is_wp_error($api_response)) {
        $result = array(
            'result' => false,
            'message' => 'Something went wrong!'
        );
        
        return $result;
//pr($api_response);
    } else {

        // Let's turn the JSON response into something easier to work with
        // I guess this part is what really answers the above in terms of WP 
        // working with json responses, if anyone knows any better way, do tell
        
        $rabattkoder_array = json_decode($api_response, true);
        // that's it
//pr($rabattkoder_array);

        $i = 0;
        
        if(!empty($rabattkoder_array)) {
            foreach ($rabattkoder_array as $rabattkod) {
                $i++;
                
                // Check if there already exists a rabattkoder-smycken post with this rabattkods-ID.
                $post = lb_get_posts_with_meta_value('wpcf-rabattkod-id', $rabattkod['id'], 'rabattkoder-smycken', array('pending', 'draft', 'future', 'private', 'publish'), '=');

                // Check if the rabattkod is for the correct affiliate network. Otherwise set $post to NULL.
                $butik = lb_get_posts_with_meta_value('wpcf-program-id', $rabattkod['programId'], 'smyckesbutiker', array('pending', 'draft', 'future', 'private', 'publish'), '=');

                if(trim(get_post_meta($butik->post->ID, 'wpcf-affiliatenatverk', true)) != $affiliate_network)
                    $post = NULL;

/*
echo('programName: ' . $rabattkod['programName'] . '<br />');
echo('programId: ' . $rabattkod['programId'] . '<br />');
echo('time: ' . time() . '<br />');
echo('startDate: ' . $rabattkod['startDate'] . '<br />');
echo('startDate: ' . get_datetime_from_epoch($rabattkod['startDate']) . '<br />');
echo('endDate: ' . get_datetime_from_epoch($rabattkod['endDate']) . '<br />');
echo('endDate: ' . date($rabattkod['endDate']) . '<br />');
pr($rabattkod, 'Rabattkod');
 * 
 * Double:
 *     [0] => Array
        (
            [id] => 20
            [program] => 680
            [code] => vÃ¥r2013
            [start_date] => 2013-04-01
            [end_date] => 2013-04-30
            [description] => 15% rabatt pÃ¥ Bust-Up brÃ¶stfÃ¶rstoring hos SthlmCompany!
GÃ¤ller fÃ¶r valfritt antal fÃ¶rpackningar Bust-Up nano gold plus under april mÃ¥nad.
Koden "vÃ¥r2013" anges i rutan fÃ¶r rabattkod i kassan.
Du hittar Bust-Up pÃ¥ fÃ¶ljande lÃ¤nk: http://www.sthlmcompany.com/bustup-brostforstoring/bust-up-nano-gold-plus.html






            [tracking] => Array
                (
                )

        )
 */

                // If there is no rabattkoder-smycken post with this rabattkods-ID we check for any smyckesbutik with the post meta wpcf-program-id=programID.
                if(empty($post->posts)) {
                    $post = lb_get_posts_with_meta_value('wpcf-program-id', $rabattkod['programId'], 'smyckesbutiker', array('publish'), '=');
                    if(!empty($post->posts)) {
                        $post = $post->posts[0];
                        $store_post_id = $post->ID;
                        $target_url = get_post_meta($store_post_id, 'wpcf-target-url', true);
                        $display_url = get_post_meta($store_post_id, 'wpcf-display-url', true);
                        $store_brand = get_post_meta($store_post_id, 'wpcf-varumarke', true);
                        // Add new rabattkoder-smycken post with all meta data.
                        $title = $rabattkod['publisherInformation'] . ' - ' . $store_brand;

                        switch($rabattkod['voucherTypeId']) {
                            case 1: // Voucher - Offer usable only with a voucher code. This type has a voucher code.
                                $title = $rabattkod['discountAmount'] . (true == $rabattkod['isPercentage']?'%':':-') . ' rabatt hos ' . $store_brand;
                                break;
                            case 2: // Discount - General discount without a voucher code. This type must always include a discount amount.
                                $title = $rabattkod['discountAmount'] . (true == $rabattkod['isPercentage']?'%':':-') . ' rabatt hos ' . $store_brand;
                                break;
                            case 3: // Free article - Free item(s) offered on purchase.
                                
                                break;
                            case 4: // Free shipping - Free shipping offered without a voucher code.
                                $title = 'Gratis frakt hos ' . $store_brand;
                                break;
                            case 5: // Raffle - Competition or lottery connected to a purchase.
                                break;
                            default:
                                $title = 'Erbjudande från ' . $store_brand;
                                break;
                        }


                        $post = array(
                            'post_title'        => $title,
                            'post_content'      => $rabattkod['description'],
                            'comment_status'    => 'closed',
                            'ping_status'       => 'closed',
                            'post_type'         => 'rabattkoder-smycken',
                            'post_status'       => 'pending',
                            'post_author'       => 2,   // Synnöve
                        );

                        $post_id = wp_insert_post($post, true);
                        
                        if(is_wp_error($post_id)) {
                            $result = array(
                                'result' => false,
                                'message' => 'Error when inserting new rabattkod post.'
                            );

                            return $result;
                        } else {
                            update_post_meta($post_id, 'wpcf-rabattkod-id', $rabattkod['id']);
                            update_post_meta($post_id, 'wpcf-store-post-id', $store_post_id);
                            update_post_meta($post_id, 'wpcf-target-url', $target_url);
                            update_post_meta($post_id, 'wpcf-display-url', $display_url);
                            update_post_meta($post_id, 'wpcf-rabattkod', $rabattkod['code']);
                            update_post_meta($post_id, 'wpcf-startdatum', round($rabattkod['startDate']/1000, 0));
                            update_post_meta($post_id, 'wpcf-slutdatum', round($rabattkod['endDate']/1000, 0));
                            update_post_meta($post_id, 'wpcf-voucher-type-id', $rabattkod['voucherTypeId']);
                            update_post_meta($post_id, 'wpcf-percentage', $rabattkod['isPercentage']);
                            update_post_meta($post_id, 'wpcf-discount-amount', $rabattkod['discountAmount']);
                            update_post_meta($post_id, 'wpcf-currency-id', $rabattkod['currencyId']);
                            update_post_meta($post_id, 'wpcf-site-specific', $rabattkod['siteSpecific']);
                            update_post_meta($post_id, 'wpcf-exclusive', $rabattkod['exclusive']);
                            
                            // Set store and brand taxonomies this post belong to.
                            $store_post_butik = wp_get_object_terms($store_post_id, 'butik');
                            $store_post_varumarken = wp_get_object_terms($store_post_id, 'varumarke');
                            
                            if(count($store_post_butik) > 0) {
                                $result = wp_set_object_terms($post_id, (int)$store_post_butik[0]->term_id, 'butik');
                            }
                            if(count($store_post_varumarken) > 0) {
                                $varumarke_id_array = array();
                                
                                foreach($store_post_varumarken as $varumarke) {
                                    array_push($varumarke_id_array, (int)$varumarke->term_id);
                                }
                                
                                $result = wp_set_object_terms($post_id, $varumarke_id_array, 'varumarke');
                            }

                            $result = array(
                                'result' => true,
                                'message' => 'Post with ID ' . $post_id . ' added and meta for rabattkod added.'
                            );

                            return $result;
                        }
                    } else {
                        $result = array(
                                'result' => false,
                                'message' => 'Affiliate ID för ' . $rabattkod['programName'] . ' hittades inte'
                            );

                        return $result;
                    }
                } else {
                    $result = array(
                        'result' => false,
                        'message' => 'Rabattkoder post with rabattkod ID ' . $rabattkod['id'] . ' already added.'
                    );

                    return $result;
                }

                /*
               echo('Message: ' . $message . '<br />');
                 
                mail('info@lyxbling.se', 'Status Rabattkoder', $message);
               
                 */
            }
        }
    }
    /*
     * id
     * programId
     * code
     * startDate
     * endDate
     * voucherTypeId
     *  1 - Voucher
     *  2 - Discount
     *  3 - Free article
     *  4 - Free shipping
     *  5 - Raffle
     * siteSpecific
     * exclusive
     * discountAmount
     * isPercentage
     * currencyId
     */
    
    return $result;
}
?>
