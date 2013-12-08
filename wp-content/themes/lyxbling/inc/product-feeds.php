<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function lb_product_feeds_setup_schedule() {
	if ( ! wp_next_scheduled( 'lb_product_feeds_daily_event' ) ) {
		wp_schedule_event( time(), 'daily', 'lb_product_feeds_daily_event');
	}
}
add_action( 'wp', 'lb_product_feeds_setup_schedule' );

/**
 * On the scheduled action hook, run a function.
 */
function lb_product_feeds_do_daily() {
    $tradedoubler_api_url = 'http://pf.tradedoubler.com/export/export?myFeed=13864207392343730&myFormat=-1';
    
    $api_response = wp_remote_retrieve_body(wp_remote_get($tradedoubler_api_url, array('sslverify' => false )));
//pr($api_response, 'api_response');
    if(is_wp_error($api_response)) {
        echo 'Something went wrong!';
    } else {

        // Let's turn the JSON response into something easier to work with
        // I guess this part is what really answers the above in terms of WP 
        // working with json responses, if anyone knows any better way, do tell
        
        $product_feed_array = simplexml_load_string($api_response);
        // that's it
//pr($product_feed_array, 'product_feed_array');

        $i = 0;
        
        if(!empty($product_feed_array)) {
            foreach ($product_feed_array as $product) {
                $i++;
//pr($product, 'product');
/*
 * [TDProductId] => 1116112705 
 * [name] => Esprit Klocka Marin Grå Aluminium 
 * [description] => ATM: 10 (100 m)Diameter: 40 mmTjocklek: 10,8 mm 
 * [imageUrl] => http://www.hedbergsguld.se/media/catalog/product/1/0/105802003_1.jpg 
 * [productUrl] => http://pdt.tradedoubler.com/click?a(2343730)p(224904)prod(1116112705)ttid(3)url(http%3A%2F%2Fwww.hedbergsguld.se%2Fesprit-klocka-marin-gra-aluminium.html) 
 * [price] => 1,649 
 * [currency] => SEK 
 * [TDCategories] => SimpleXMLElement Object ( 
 *      [TDCategory] => SimpleXMLElement Object ( 
 *              [id] => 70 [name] => Smycken 
 *              [merchantName] => Varumärken/Esprit 
 *      ) 
 * ) 
 * [fields] => SimpleXMLElement Object ( ) - See more at: http://lyxbling.dev/om#sthash.i5ZS3bev.dpuf
 */
            }
        }
    }
}
//add_action('init', 'lb_product_feeds_do_daily');
//add_action( 'lb_product_feeds_daily_event', 'lb_product_feeds_do_daily' );
?>
