<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/*
 * Function to display information below each online store.
 */
function lb_display_below_post_info() {
    $post_type = get_post_type();
    
    if(is_single()) {
        switch($post_type) {
            case 'smyckesbutiker':
                echo(lb_get_link_button(get_the_ID()));
                echo(lb_get_trust_symbols());
                echo(lb_get_facts_contact());
                //echo(lb_get_social_media_links());
                echo(lb_get_payment_options());
                //echo(lb_get_freight());
                //echo(lb_get_brands_sold($the_post->ID, $the_post->post_name));
                echo(lb_get_link_button(get_the_ID()));
                break;
            case 'smyckesvarumarken':
                echo(lb_get_link_button(get_the_ID()));
                echo(lb_get_facts_contact());
                //echo(lb_get_social_media_links());
                echo(lb_get_link_button(get_the_ID()));
                break;
            case 'presenttips':
                echo(lb_get_link_button(get_the_ID()));
            default:
                break;
        }
        
        if(!is_front_page()) {
                echo(lb_get_social_share_buttons());
                //lb_get_outgoing_competitions();
        }
    }
}
add_action('woo_post_inside_after', 'lb_display_below_post_info');

function lb_get_facts_contact() {
    $post_id = get_the_ID();
    
    if('' != trim(lb_get_post_meta($post_id, 'varumarke'))) {
        $brand = trim(lb_get_post_meta($post_id, 'varumarke'));
        $brand_microformats = '<span itemprop="brand">' . $brand . '</span>';
    }
    
    $html_output = '<div class="entry" itemscope itemtype="http://schema.org/Organization"><h2>Fakta &amp; kontaktuppgifter för ' . $brand_microformats . '</h2>';
    
    if('' != trim(lb_get_post_meta($post_id, 'fakta-wysiwyg')))
        $html_output .= '<div id="fakta-fritext" itemprop="description">' . lb_get_post_meta($post_id, 'fakta-wysiwyg', $args = array('output' => 'html')) . '</div>';
    if('' != trim(lb_get_link($post_id))) {
        $homepage_link = lb_get_link($post_id);
        
        $html_output .= '<strong>Hemsida:</strong> ' . $homepage_link . '<br /><br />';
    }
    if('' != trim(lb_get_post_meta($post_id, 'gatuadress'))) 
        $html_output .= '<strong>Adress:</strong><br /><section itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span class="fn org">' . lb_get_post_meta($post_id, 'foretagsnamn') . '</span><br /><span itemprop="streetAddress">' . lb_get_post_meta($post_id, 'gatuadress') . '</span><br /><span itemprop="postalCode">' . substr_replace(lb_get_post_meta($post_id, 'postnummer'), ' ', 3, 0) . '</span> <span itemprop="addressLocality">' . lb_get_post_meta($post_id, 'postadress') . '</span></section><br />';
    if('' != trim(lb_get_post_meta($post_id, 'telefon')))
        $html_output .= '<strong>Telefon:</strong> <span itemprop="telephone">' . lb_get_post_meta($post_id, 'telefon') . '</span><br /><br />';
    if('' != trim(lb_get_post_meta($post_id, 'e-post')))
        $html_output .= '<strong>E-post:</strong> <a href="mailto:' . lb_get_post_meta($post_id, 'e-post') . '" itemprop="email">' . lb_get_post_meta($post_id, 'e-post') . '</a><br />';
    if('' != trim(lb_get_post_meta($post_id, 'organisationsnummer')))
        $html_output .= '<strong>Organisationsnummer:</strong> <span itemprop="taxID">' . lb_get_post_meta($post_id, 'organisationsnummer') . '</span><br />';
    if('' != trim(lb_get_post_meta($post_id, 'omsattning')))
        $html_output .= '<strong>Omsättning:</strong> <span>' . lb_get_post_meta($post_id, 'omsattning') . '</span><br />';
    if('' != trim(lb_get_post_meta($post_id, 'antal-anstallda')))
        $html_output .= '<strong>Antal anställda:</strong> <span>' . lb_get_post_meta($post_id, 'antal-anstallda') . '</span><br />';
    
    $html_output .= '</div><!- itemscope itemtype="http://schema.org/Organization" -->';
    
    return $html_output;
}

function lb_get_customer_service() {
    $post_id = get_the_ID();
}

function lb_get_payment_options() {
    $post_id = get_the_ID();
    
    $payment_options = get_post_meta($post_id, 'wpcf-betalningsalternativ');
    $payment_options = $payment_options[0];
    $html_output = '';
    
    if(is_array($payment_options) && in_array('1', $payment_options)) {
        if('' != trim(lb_get_post_meta($post_id, 'varumarke'))) {
            $brand = trim(lb_get_post_meta($post_id, 'varumarke'));
            $brand_microformats = '<span itemprop="brand">' . $brand . '</span>';
        }

        $html_output = '<div class="entry" itemscope itemtype="http://schema.org/Organization"><h2>Betalningsalternativ på ' . $brand_microformats . '</h2>';
        
        if($payment_options['wpcf-fields-checkboxes-option-f6292b400bcab1ad3f914a7204a9b17c-2'] == 1)
            $html_output .= '<div class="mastercard">Mastercard</div>';
        if($payment_options['wpcf-fields-checkboxes-option-868757a803345a5f63060ac7ebbac514-1'] == 1)
            $html_output .= '<div class="visa">VISA</div>';
        if($payment_options['wpcf-fields-checkboxes-option-cfe5246aa4ab54277c1431b7602778eb-1'] == 1)
            $html_output .= '<div class="american-express">American Express</div>';
        if($payment_options['wpcf-fields-checkboxes-option-bc51b8a8047b0f767bfbe8412fe3519c-1'] == 1)
            $html_output .= '<div class="paypal">PayPal</div>';
        if($payment_options['wpcf-fields-checkboxes-option-d0e1a715693d3430de1a235d6e929808-1'] == 1)
            $html_output .= '<div class="visa-electron">VISA Electron</div>';
        if($payment_options['wpcf-fields-checkboxes-option-f2d59389cf77c1bbe6cefcd51b9b9a52-1'] == 1)
            $html_output .= '<div class="maestro">Maestro</div>';
        if($payment_options['wpcf-fields-checkboxes-option-53ee90cea0eea9846e906c93418037ea-1'] == 1)
            $html_output .= '<div class="klarna">Klarna Faktura</div>';
        if($payment_options['wpcf-fields-checkboxes-option-92c5780b770329cd3e88206af382daeb-1'] == 1)
            $html_output .= '<div class="klarna">Klarna Konto</div>';
        if($payment_options['wpcf-fields-checkboxes-option-632c6bf0f9e44c9bf40d3cc4f3be583a-1'] == 1)
            $html_output .= '<div class="klarna">Klarna Mobil</div>';
        if($payment_options['wpcf-fields-checkboxes-option-9a51c1802a107a9d1e47468a01c004a6-1'] == 1)
            $html_output .= '<div class="klarna">Payson</div>';
        if($payment_options['wpcf-fields-checkboxes-option-51c77e305abd37ae6eb42a50ced88c1a-1'] == 1)
            $html_output .= '<div class="klarna">SweWebPay</div>';
        if($payment_options['wpcf-fields-checkboxes-option-cc8e76a21234fff851e5407e8d827e3d-1'] == 1)
            $html_output .= '<div class="postforskott">Postförskott</div>';

        $html_output .= '</div><!- itemscope itemtype="http://schema.org/Organization" -->';
    }
    
    return $html_output;
}

function lb_get_freight() {
    $post_id = get_the_ID();
    
    $html_output = '<div class="entry" itemscope itemtype="http://schema.org/DeliveryChargeSpecification"><h2>Fraktkostnader &amp; Leveranstid</h2>';
    
    if('' != trim(lb_get_post_meta($post_id, 'frakt-wysiwyg')))
        $html_output .= '<div id="frakt-fritext" itemprop="description">' . lb_get_post_meta($post_id, 'frakt-wysiwyg', $args = array('output' => 'html')) . '</div>';
    if('' != trim(lb_get_post_meta($post_id, 'fraktkostnad-normal'))) 
        $html_output .= '<strong>Normal fraktkostnad:</strong> <span itemprop="price">' . lb_get_post_meta($post_id, 'fraktkostnad-normal') . ':-</span><br />';
    if('' != trim(lb_get_post_meta($post_id, 'fraktkostnad-gratis-grans')))
        $html_output .= '<strong>Minsta ordervärde för fri frakt:</strong> <span itemprop="eligibleTransactionVolume">' . lb_get_post_meta($post_id, 'fraktkostnad-gratis-grans') . ':-</span><br />';
    if('' != trim(lb_get_post_meta($post_id, 'faktureringsavgift')))
        $html_output .= '<strong>Faktureringsavgift:</strong> <span>' . lb_get_post_meta($post_id, 'faktureringsavgift') . ':-</span><br />';
    if('' != trim(lb_get_post_meta($post_id, 'postforskottsavgift')))
        $html_output .= '<strong>Postförskottsavgift:</strong> <span>' . lb_get_post_meta($post_id, 'postforskottsavgift') . ':-</span><br />';
    if('' != trim(lb_get_post_meta($post_id, 'leveranstid')))
        $html_output .= '<strong>Normal leveranstid:</strong> <span>' . lb_get_post_meta($post_id, 'leveranstid') . ' arbetsdagar</span><br />';
    
    $html_output .= '</div><!- itemscope itemtype="http://schema.org/DeliveryChargeSpecification" -->';
    
    return $html_output;
}

function lb_get_trust_symbols() {
    $post_id = get_the_ID();
    $html_output = '';
    
    if('' != trim(lb_get_post_meta($post_id, 'trygg-ehandel-butiks-id')) || '' != trim(lb_get_post_meta($post_id, 'e-handelscertifiering-butiks-url'))) {
        if('' != trim(lb_get_post_meta($post_id, 'varumarke'))) {
            $brand = trim(lb_get_post_meta($post_id, 'varumarke'));
            $brand_microformats = '<span itemprop="brand">' . $brand . '</span>';
        }

        $html_output = '<div class="entry"><h2>Trygghet</h2>';

        if('' != trim(lb_get_post_meta($post_id, 'trygg-ehandel-butiks-id'))) 
            $html_output .= '<a href="http://www.tryggehandel.se/butik/' . lb_get_post_meta($post_id, 'trygg-ehandel-butiks-id') . '" target="_blank"><img src="http://www.tryggehandel.se/images/teh_logo_68.jpg" alt="Trygg e-handel certifikat för ' . $brand . '" width="56" /></a>';
        if('' != trim(lb_get_post_meta($post_id, 'e-handelscertifiering-butiks-url'))) 
            $html_output .= '<a href="https://www.ehandelscertifiering.se/rapport.php?url=' . lb_get_post_meta($post_id, 'e-handelscertifiering-butiks-url') . '" target="_blank"><img src="https://www.ehandelscertifiering.se/lv5/logotyp.php?size=56&lang=se&autolang=yes&url=' . lb_get_post_meta($post_id, 'e-handelscertifiering-butiks-url') . '&track=no" alt="Certifierad e-handel för ' . $brand . '" width="56" /></a>';

        $html_output .= '</div>';
    }
    
    return $html_output;
}

function lb_get_social_media_links() {
    $post_id = get_the_ID();
    
    $html_output = ''; // '<div class="social-buttons"><h2>Sociala mediakanaler</h2>';
    if('' != trim(lb_get_post_meta($post_id, 'facebook-url-id'))) 
        $html_output .= '<a class="svg webicon facebook" target="_blank" href="https://www.facebook.com/' . lb_get_post_meta($post_id, 'facebook-url-id') . '" class="facebook" title="Facebook"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'twitter-url-id'))) 
        $html_output .= '<a class="no-svg webicon twitter" target="_blank" href="https://twitter.com/' . lb_get_post_meta($post_id, 'twitter-url-id') . '" class="twitter" title="Twitter"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'google-url-id'))) 
        $html_output .= '<a class="webicon googleplus" target="_blank" href="https://plus.google.com/' . lb_get_post_meta($post_id, 'google-url-id') . '" class="googleplus" title="Google+"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'pinterest-url-id'))) 
        $html_output .= '<a class="webicon pinterest" target="_blank" href="http://www.pinterest.com/' . lb_get_post_meta($post_id, 'pinterest-url-id') . '/" class="pinterest" title="Pinterest"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'instagram-url-id'))) 
        $html_output .= '<a class="webicon instagram" target="_blank" href="http://instagram.com/' . lb_get_post_meta($post_id, 'instagram-url-id') . '" class="instagram" title="Instagram"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'flickr-url-id'))) 
        $html_output .= '<a class="webicon flickr" target="_blank" href="http://www.flickr.com/people/com/' . lb_get_post_meta($post_id, 'flickr-url-id') . '/" class="flickr" title="Flickr"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'youtube-url-id'))) 
        $html_output .= '<a class="webicon youtube" target="_blank" href="http://www.youtube.com/' . lb_get_post_meta($post_id, 'youtube-url-id') . '" class="youtube" title="YouTube"></a>';
    if('' != trim(lb_get_post_meta($post_id, 'vimeo-url-id'))) 
        $html_output .= '<a class="webicon vimeo" target="_blank" href="http://vimeo.com/' . lb_get_post_meta($post_id, 'youtube-url-id') . '" class="youtube" title="YouTube"></a>';
    
    $html_output .= '</div>';
    
    return $html_output;
}

function lb_get_others() {
    $post_id = get_the_ID();
}
?>