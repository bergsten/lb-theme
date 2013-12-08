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
    // https://api.tradedoubler.com/1.0/vouchers.json;siteSpecific=false?token=FF47AC9656810C3F795E2407E9B67828D1B7561F
    $voucher_key = 'FF47AC9656810C3F795E2407E9B67828D1B7561F';
    $tradedoubler_api_url = 'https://api.tradedoubler.com/1.0/vouchers.json;siteSpecific=false?token=' . $voucher_key;
    
    $api_response = wp_remote_retrieve_body(wp_remote_get($tradedoubler_api_url, array('sslverify' => false )));

    if(is_wp_error($api_response)) {
        echo 'Something went wrong!';
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
/*
echo('programName: ' . $rabattkod['programName'] . '<br />');
echo('programId: ' . $rabattkod['programId'] . '<br />');
echo('time: ' . time() . '<br />');
echo('startDate: ' . $rabattkod['startDate'] . '<br />');
echo('startDate: ' . get_datetime_from_epoch($rabattkod['startDate']) . '<br />');
echo('endDate: ' . get_datetime_from_epoch($rabattkod['endDate']) . '<br />');
echo('endDate: ' . date($rabattkod['endDate']) . '<br />');
pr($rabattkod, 'Rabattkod');
 */

                // If there is no rabattkoder-smycken post with this rabattkods-ID we check for any smyckesbutik with the post meta wpcf-publisher-id=programID.
                if(empty($post->posts)) {
                    $post = lb_get_posts_with_meta_value('wpcf-publisher-id', $rabattkod['programId'], 'smyckesbutiker', array('publish'), '=');
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
                            $message = 'Error when inserting new rabattkod post.';
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

                            $message = 'Post with ID ' . $post_id . ' added and meta for rabattkod added.';
                        }
                    } else {
                        $message = 'Affiliate ID för ' . $rabattkod['programName'] . ' hittades inte';
                    }
                } else {
                    $message = 'Rabattkoder post with rabattkod ID ' . $rabattkod['id'] . ' already added.';
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
}
//add_action('init', 'lb_rabattkoder_do_daily');
add_action( 'lb_rabattkoder_daily_event', 'lb_rabattkoder_do_daily' );
?>
