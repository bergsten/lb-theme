<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Customise the fields in the WooFramework "Theme Options". See http://cliftonhatfield.com/customizing-woothemes-options-panel/
function lb_woo_options_add() {
    if(is_admin()) {
        global $woo_options;
//pr($woo_options);
        if(is_array($woo_options)) {

            $shortname = 'woo';
            $i = 0;
            $array = array();

        
            while(list($key, $value) = each($woo_options)) {
                    if($value['name'] == __( 'Disable Archive Header RSS link', 'woothemes' )) {
                            $array[$i] = $value;
                            $i++;

                            $array[$i] = array( "name" => __( 'Archive Page Layout', 'woothemes' ),
                                            "desc" => __( 'Select main content and sidebar alignment for archive pages. Choose between 1, 2 or 3 column layout.', 'woothemes' ),
                                            "id" => $shortname . "_wc_archive_layout",
                                            "std" => "two-col-left",
                                            "type" => "images",
                                            "options" => array(
                                                    'one-col' => $images_dir . '1c.png',
                                                    'two-col-left' => $images_dir . '2cl.png',
                                                    'two-col-right' => $images_dir . '2cr.png',
                                                    'three-col-left' => $images_dir . '3cl.png',
                                                    'three-col-middle' => $images_dir . '3cm.png',
                                                    'three-col-right' => $images_dir . '3cr.png')
                                            );
                    } else {
                            $array[$i] = $value;
                    }

                    $i++;
            }
        }
        
        $woo_options = $array;
    }
}
//add_filter( 'woo_options_add', 'lb_woo_options_add', 10, 1 );
add_action('after_setup_theme', 'lb_woo_options_add', 10, 1)
?>