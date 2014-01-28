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
    $post_id = get_the_ID();
    $post_type = get_post_type();
    $link_button = lb_get_link_button($post_id);
    
    if(is_single() || is_page()) {
        switch($post_type) {
            case 'smyckesbutiker':
                echo($link_button);
                echo(lb_get_rabattkoder($post_id));
                echo(lb_get_competitions($post_id));
                echo(lb_get_trust_symbols($post_id));
                echo(lb_get_facts_contact($post_id));
                echo(lb_get_social_media_links($post_id));
                echo(lb_get_payment_options($post_id));
                //echo(lb_get_freight($post_id));
                echo(lb_get_brands_sold_by_store($post_id));
                echo($link_button);
                break;
            case 'smyckesvarumarken':
                echo($link_button);
                echo(lb_get_facts_contact($post_id));
                echo(lb_get_social_media_links($post_id));
                echo(lb_get_stores_selling_brand($post_id));
                echo($link_button);
                break;
            case 'presenttips':
                echo($link_button);
                break;
            case 'smyckesevent':
                echo($link_button);
                break;
            case 'smyckeserbjudanden':
                echo($link_button);
                break;
            case 'smyckestavlingar':
                echo($link_button);
                break;
            default:
                echo($link_button);
                break;
        }
        
        if(!is_front_page()) {
                echo(lb_get_social_share_buttons($post_id));
        }
    }
}
add_action('woo_post_inside_after', 'lb_display_below_post_info');

function lb_get_facts_contact($post_id = NULL) {
    if(!$post_id)
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
    if('' != trim(lb_get_post_meta($post_id, 'gatuadress')) || '' != trim(lb_get_post_meta($post_id, 'postbox'))) {
        $address = '';
        $country = '';
        if('' != trim(lb_get_post_meta($post_id, 'gatuadress')))
            $address .= '<span itemprop="streetAddress">' . lb_get_post_meta($post_id, 'gatuadress') . '</span><br />';
        if('' != trim(lb_get_post_meta($post_id, 'postbox')))
            $address .= 'Box <span itemprop="postOfficeBoxNumber">' . lb_get_post_meta($post_id, 'postbox') . '</span><br />';
        if('' != trim(lb_get_post_meta($post_id, 'country')) && 'sweden' != trim(lb_get_post_meta($post_id, 'country')))
            $country .= '<br /><span itemprop="addressCountry">' . ucfirst(lb_get_post_meta($post_id, 'country')) . '</span><br />';
        $html_output .= '<strong>Adress:</strong><br /><section itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="name">' . lb_get_post_meta($post_id, 'foretagsnamn') . '</span><br />' . $address . '<span itemprop="postalCode">' . lb_format_postal_number(lb_get_post_meta($post_id, 'postnummer')) . '</span> <span itemprop="addressLocality">' . lb_get_post_meta($post_id, 'postadress') . '</span>' . $country . '</section><br />';
    }
    if('' != trim(lb_get_post_meta($post_id, 'telefon')))
        $html_output .= '<strong>Telefon:</strong> <span><a itemprop="telephone" href="tel:' . lb_format_phone_number_link(lb_get_post_meta($post_id, 'telefon')) . '">' . lb_format_phone_number(lb_get_post_meta($post_id, 'telefon')) . '</a></span><br /><br />';
    if('' != trim(lb_get_post_meta($post_id, 'e-post')))
        $html_output .= '<strong>E-post:</strong> <a href="mailto:' . lb_get_post_meta($post_id, 'e-post') . '" itemprop="email">' . lb_get_post_meta($post_id, 'e-post') . '</a><br />';
    if('' != trim(lb_get_post_meta($post_id, 'organisationsnummer')))
        $html_output .= '<br /><strong>Organisationsnummer:</strong> <span itemprop="taxID">' . lb_get_post_meta($post_id, 'organisationsnummer') . '</span><br />';
    if('' != trim(lb_get_post_meta($post_id, 'omsattning')))
        $html_output .= '<strong>Omsättning:</strong> <span>' . lb_get_post_meta($post_id, 'omsattning') . ' miljoner kronor</span><br />';
    if('' != trim(lb_get_post_meta($post_id, 'antal-anstallda')))
        $html_output .= '<strong>Antal anställda:</strong> <span>' . lb_get_post_meta($post_id, 'antal-anstallda') . '</span><br />';
    
    $html_output .= '</div><br /><!- itemscope itemtype="http://schema.org/Organization" -->';
    
    return $html_output;
}

function lb_get_customer_service() {
    $post_id = get_the_ID();
}

function lb_get_payment_options($post_id = NULL) {
    if(!$post_id)
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

function lb_get_freight($post_id = NULL) {
    if(!$post_id)
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

function lb_get_trust_symbols($post_id = NULL) {
    if(!$post_id)
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

function lb_get_social_media_links($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    $html_output = '';
    
    if('' != trim(lb_get_post_meta($post_id, 'facebook-url-id'))
            || '' != trim(lb_get_post_meta($post_id, 'twitter-url-id'))
            || '' != trim(lb_get_post_meta($post_id, 'google-url-id'))
            || '' != trim(lb_get_post_meta($post_id, 'pinterest-url-id'))
            || '' != trim(lb_get_post_meta($post_id, 'instagram-url-id'))
            || '' != trim(lb_get_post_meta($post_id, 'flickr-url-id'))
            || '' != trim(lb_get_post_meta($post_id, 'youtube-url-id'))
            || '' != trim(lb_get_post_meta($post_id, 'vimeo-url-id'))
            || '' != trim(lb_get_post_meta($post_id, 'linkedin-url-id'))
    ) {
        $brand = trim(lb_get_post_meta($post_id, 'varumarke'));

        $html_output = '<div class="social-buttons"><strong>' . $brand . ' på sociala medier</strong><br />'; // '<div class="social-buttons"><h2>Sociala mediakanaler</h2>';
        if('' != trim(lb_get_post_meta($post_id, 'facebook-url-id'))) 
            $html_output .= '<a class="webicon facebook" target="_blank" href="https://www.facebook.com/' . lb_get_post_meta($post_id, 'facebook-url-id') . '" class="facebook" title="' . $brand . ' på Facebook">' . $brand . ' på Facebook</a>';
        if('' != trim(lb_get_post_meta($post_id, 'twitter-url-id'))) 
            $html_output .= '<a class="webicon twitter" target="_blank" href="https://twitter.com/' . lb_get_post_meta($post_id, 'twitter-url-id') . '" class="twitter" title="' . $brand . ' på Twitter">' . $brand . ' på Twitter</a>';
        if('' != trim(lb_get_post_meta($post_id, 'google-url-id'))) 
            $html_output .= '<a class="webicon googleplus" target="_blank" href="https://plus.google.com/' . lb_get_post_meta($post_id, 'google-url-id') . '" class="googleplus" title="' . $brand . ' på Google+">' . $brand . ' på Google+</a>';
        if('' != trim(lb_get_post_meta($post_id, 'pinterest-url-id'))) 
            $html_output .= '<a class="webicon pinterest" target="_blank" href="http://www.pinterest.com/' . lb_get_post_meta($post_id, 'pinterest-url-id') . '/" class="pinterest" title="' . $brand . ' på Pinterest">' . $brand . ' på Pinterest</a>';
        if('' != trim(lb_get_post_meta($post_id, 'instagram-url-id'))) 
            $html_output .= '<a class="webicon instagram" target="_blank" href="http://instagram.com/' . lb_get_post_meta($post_id, 'instagram-url-id') . '" class="instagram" title="' . $brand . ' på Instagram">' . $brand . ' på Instagram</a>';
        if('' != trim(lb_get_post_meta($post_id, 'flickr-url-id'))) 
            $html_output .= '<a class="webicon flickr" target="_blank" href="http://www.flickr.com/people/com/' . lb_get_post_meta($post_id, 'flickr-url-id') . '/" class="flickr" title="' . $brand . ' på Flickr">' . $brand . ' på Flickr</a>';
        if('' != trim(lb_get_post_meta($post_id, 'youtube-url-id'))) 
            $html_output .= '<a class="webicon youtube" target="_blank" href="http://www.youtube.com/' . lb_get_post_meta($post_id, 'youtube-url-id') . '" class="youtube" title="' . $brand . ' på YouTube">' . $brand . ' på YouTube</a>';
        if('' != trim(lb_get_post_meta($post_id, 'vimeo-url-id'))) 
            $html_output .= '<a class="webicon vimeo" target="_blank" href="http://vimeo.com/' . lb_get_post_meta($post_id, 'vimeo-url-id') . '" class="vimeo" title="' . $brand . ' på Vimeo">' . $brand . ' på Vimeo</a>';
        if('' != trim(lb_get_post_meta($post_id, 'linkedin-url-id'))) 
            $html_output .= '<a class="webicon linkedin" target="_blank" href="http://www.linkedin.com/company/' . lb_get_post_meta($post_id, 'linkedin-url-id') . '" class="linkedin" title="' . $brand . ' på LinkedIn">' . $brand . ' på LinkedIn</a>';

        $html_output .= '</div>';
    }
    
    return $html_output;
}

function lb_get_others($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
}

function lb_get_brands_sold_by_store($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    $html_output = '';
    
    $query = lb_get_related_posts_by_taxonomy($post_id, 'varumarke', 'smyckesvarumarken');
    
    if(!empty($query) && $query->have_posts()) {
        $html_output .= '<h2>Smyckesvarumärken hos ' . lb_get_post_meta($post_id, 'varumarke') . '</h2>';
        
        while($query->have_posts()) {
            $query->the_post();
            
            $html_output .= '<div><a href="' . get_permalink(get_the_ID()) . '">' . lb_get_post_meta(get_the_ID(), 'varumarke') . '</a></div> ';
        }
    }
    
    wp_reset_query();
    
    return $html_output;
}

function lb_get_stores_selling_brand($post_id = NULL) {
    if(!$post_id)
        $post_id = get_the_ID();
    
    $html_output = '';
    
    $query = lb_get_related_posts_by_taxonomy($post_id, 'butik', 'smyckesbutiker');
    
    if(!empty($query) && $query->have_posts()) {
        $html_output .= '<h2>Butiker som saluför smycken från ' . lb_get_post_meta($post_id, 'varumarke') . '</h2>';
        
        while($query->have_posts()) {
            $query->the_post();
            
            $html_output .= '<div><a href="' . get_permalink(get_the_ID()) . '">' . lb_get_post_meta(get_the_ID(), 'varumarke') . '</a></div> ';
        }
    }
    
    wp_reset_query();
    
    return $html_output;
}

function lb_get_rabattkoder($post_id = NULL) {
    global $woo_options;
    
    $thumb_width = 210;
    $thumb_height = 120;
    
    if(!$post_id)
        $post_id = get_the_ID();
    
    $html_output = '';
    
    $query = lb_get_related_posts_by_taxonomy($post_id, 'butik', 'rabattkoder-smycken');
    
    if(!empty($query) && $query->have_posts()) {
        $html_output .= '<h2>Rabattkoder hos ' . lb_get_post_meta($post_id, 'varumarke') . '</h2>';
        
        while($query->have_posts()) {
            $query->the_post();
            
            /* Setup image for display and for checks, to avoid doing multiple queries. */
            $woo_image = woo_image( 'return=true&key=image&class=thumbnail alignleft&width=' . $thumb_width . '&height=' . $thumb_height . '&link=img&alt=' . get_the_title() . '' );
            if ( $woo_image != '' ) {
		$html_output .= '<a ' . $settings['rel'] . ' title="' . get_the_title() . '" href="' . get_permalink(get_the_ID()) . '" class="thumb">' . $woo_image . '</a>';
            }
            
            $html_output .= '<h3><a href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a></h3>';
            $html_output .= strip_tags(get_the_excerpt());
            $html_output .= '<br clear="all" /><div class="lb-button right" style="clear: both;" data-url="' . get_permalink(get_the_ID()) . '">' . __( 'Continue Reading &raquo;', 'woothemes' ) . '</div><br clear="all" />';
            $html_output .= '<div><a href="' . get_permalink(get_the_ID()) . '">' . lb_get_post_meta(get_the_ID(), 'varumarke') . '</a></div> ';
        }
    }
    
    wp_reset_query();
    
    return $html_output;
}

function lb_get_competitions($post_id = NULL) {
    global $woo_options;
    
    $thumb_width = 210;
    $thumb_height = 120;
    
    if(!$post_id)
        $post_id = get_the_ID();
    
    $html_output = '';
    
    $query = lb_get_related_posts_by_taxonomy($post_id, 'butik', 'smyckestavlingar');
    
    if(!empty($query) && $query->have_posts()) {
        $html_output .= '<h2>Smyckestävlingar hos ' . lb_get_post_meta($post_id, 'varumarke') . '</h2>';
        
        while($query->have_posts()) {
            $query->the_post();
            
            $woo_image = woo_image( 'return=true&key=image&class=thumbnail alignleft&width=' . $thumb_width . '&height=' . $thumb_height . '&link=img&alt=' . get_the_title() . '' );
            if ( $woo_image != '' ) {
		$html_output .= '<a ' . $settings['rel'] . ' title="' . get_the_title() . '" href="' . get_permalink(get_the_ID()) . '" class="thumb">' . $woo_image . '</a>';
            }
            $html_output .= '<h3><a href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a></h3>';
            $html_output .= strip_tags(get_the_excerpt());
            $html_output .= '<br clear="all" /><div class="lb-button right" style="clear: both;" data-url="' . get_permalink(get_the_ID()) . '">' . __( 'Continue Reading &raquo;', 'woothemes' ) . '</div><br clear="all" />';
            $html_output .= '<div><a href="' . get_permalink(get_the_ID()) . '">' . lb_get_post_meta(get_the_ID(), 'varumarke') . '</a></div> ';
        }
    }
    
    wp_reset_query();
    
    return $html_output;
}

function lb_format_postal_number($num) {
    $len = strlen($num);
    
    if($len == 5) $num = substr_replace($num, ' ', 3, 0);
    
    return $num;
}

function lb_format_phone_number($num) {
    // If number starts with 0 it's a Swedish phone number.
    if ('0' == substr($num, 0, 1)) {
        $num = preg_replace('/[^0-9]/', '', $num);
        $area_code = preg_replace('/0(10|20|70|72|73|760|76|741|742|743|744|745|747|271|322|174|472|371|589|961|960|570|583|226|624|915|531|652|932|662|921|278|243|33|142|661|456|693|914|912|431|571|295|586|552|653|942|534|271|381|471|171|246|16|413|223|346|515|23|590|122|585|157|241|943|684|258|528|645|241|493|371|158|498|525|555|591|390|514|551|672|970|693|26|31|511|563|975|643|582|220|175|35|696|644|297|922|928|224|684|225|291|42|513|301|503|290|671|506|650|495|36|663|345|591|611|451|42|491|415|413|253|247|971|621|647|36|916|923|480|505|454|294|586|455|54|150|554|320|980|494|435|580|977|612|44|550|640|226|19|300|227|303|221|430|925|418|584|247|474|302|478|692|510|581|13|642|372|651|657|240|920|46|950|523|913|157|40|280|953|496|159|501|433|530|142|553|250|141|392|524|563|499|587|223|930|11|176|918|512|481|155|380|622|297|454|250|304|479|491|643|155|978|435|911|973|623|175|934|457|459|472|924|682|248|224|26|414|416|511|910|222|142|294|500|240|620|952|8|951|290|152|433|526|670|695|60|565|220|418|585|680|325|687|246|564|533|253|225|382|293|270|121|456|504|502|293|60|477|304|417|691|560|16|486|345|325|140|410|520|156|954|292|506|522|613|321|90|18|143|393|156|123|281|512|340|383|125|940|492|933|151|495|498|981|976|322|521|935|370|490|21|470|411|571|532|690|647|573|474|941|120|476|251|929|431|144|485|295|19|173|660|291|63|292|173|926|927)(\d*)/', '0$1', $num);
        $number = substr($num, strlen($area_code));
        $len = strlen($number);
        $num = $area_code . ' - ';
        
        if($len == 5) $num .= preg_replace('/([0-9]{3})([0-9]{2})/', '$1 $2', $number);
        elseif($len == 6) $num .= preg_replace('/([0-9]{2})([0-9]{2})([0-9]{2})/', '$1 $2 $3', $number);
        elseif($len == 7) $num .= preg_replace('/([0-9]{3})([0-9]{2})([0-9]{2})/', '$1 $2 $3', $number);
        elseif($len == 8) $num .= preg_replace('/([0-9]{3})([0-9]{3})([0-9]{2})/', '$1 $2 $3', $number);
    // If number starts with a + it's an international number.
    } else if('+' == substr($num, 0, 1)) {
        
    }
    
    return $num;
}

function lb_format_phone_number_link($num) {
    if ('0' == substr($num, 0, 1)) {
        $num = '+46' . substr($num, 1);        
    } else if('+' == substr($num, 0, 1)) {
        
    }
    
    return $num;
}
?>
