<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Add extra ad formats that we need.
 */
function lb_filter_ad_tag_ids( $ids ) {
 
    $ids[] = array(
        'tag' => '300x250-btf',
        'url_vars' => array(
            'sz' => '300x250',
            'fold' => 'btf',
            'width' => '300',
            'height' => '250',
        )
    );
    
    $ids[] = array(
        'tag' => '1x1',
        'url_vars' => array(
                'sz' => '1x1',
                'fold' => 'int',
                'pos' => 'top',
        )
    );
 
    return $ids;
}
//add_filter( 'acm_ad_tag_ids', 'lb_filter_ad_tag_ids' );
?>
