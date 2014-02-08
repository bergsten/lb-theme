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
    $product_feed = lb_get_product_feed_id(840);
    $tradedoubler_api_url = 'http://pf.tradedoubler.com/export/export?myFeed=' . $product_feed . '&myFormat=-1';
echo('tradedoubler_api_url='.$tradedoubler_api_url.'<br>');

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
pr($product, 'product');
exit;

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
 * 
 * 
 * 
 * 
    [name] => Diamondo Vigselring 18k Vitguld
    [productUrl] => http://pdt.tradedoubler.com/click?a(2343730)p(224904)prod(1116112333)ttid(5)url(http%3A%2F%2Fwww.hedbergsguld.se%2Fdiamondo-vigselring-18k-vitguld.html)
    [imageUrl] => http://www.hedbergsguld.se/media/catalog/product/7/5/753699.jpg
    [description] => Material : 18 karats vitt guldBredd : 4mmHÃ¶jd : 1,2mmDiamanter : 5x0,01 carat Wesselton Si
    [price] => 3698.00
    [currency] => SEK
    [TDProductId] => 1116112333
    [TDCategoryID] => 70
    [TDCategoryName] => Smycken
    [merchantCategoryName] => Vigselringar
    [sku] => 753699
    [shortDescription] => Exklusiv vigselring i 18 karat vitguld frÃ¥n Diamondo. Vigselringen har fem stycken diamanter infattade i fyrkanter mitt uppe pÃ¥ skenan. Skenan Ã¤r 4 mm bred, 1,2mm hÃ¶g med nÃ¥got kupad skena. Gravyr ingÃ¥r i vigselringen.
    [promoText] => SimpleXMLElement Object
        (
        )

    [previousPrice] => SimpleXMLElement Object
        (
        )

    [warranty] => SimpleXMLElement Object
        (
        )

    [availability] => 1
    [inStock] => SimpleXMLElement Object
        (
        )

    [shippingCost] => SimpleXMLElement Object
        (
        )

    [deliveryTime] => SimpleXMLElement Object
        (
        )

    [weight] => SimpleXMLElement Object
        (
        )

    [size] => SimpleXMLElement Object
        (
        )

    [brand] => SimpleXMLElement Object
        (
        )

    [model] => SimpleXMLElement Object
        (
        )

    [ean] => SimpleXMLElement Object
        (
        )

    [upc] => SimpleXMLElement Object
        (
        )

    [isbn] => SimpleXMLElement Object
        (
        )

    [condition] => SimpleXMLElement Object
        (
        )

    [mpn] => SimpleXMLElement Object
        (
        )

    [techSpecs] => SimpleXMLElement Object
        (
        )

    [manufacturer] => SimpleXMLElement Object
        (
        )

    [programName] => Hedbergs Guld & Silver
    [programLogoPath] => http://hst.tradedoubler.com/file/224904/logos/hedbergs_logo_100.gif
    [programId] => 224904
    [fields] => SimpleXMLElement Object
        (
        )
 */

            }
        }
    }
}
//add_action('loop_start', 'lb_product_feeds_do_daily');
//add_action( 'lb_product_feeds_daily_event', 'lb_product_feeds_do_daily' );

function lb_get_product_feed_id($post_id) {
    if(!$post_id)
        return false;

    return get_post_meta($post_id, 'wpcf-egen-product-feed-id', true);
}
?>
