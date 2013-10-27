/* 
 * jQuery function to open external site link on button click.
 * From http://stackoverflow.com/questions/4944387/go-to-link-on-button-click-jquery/4944460#4944460
 */
jQuery(document).ready(function(){
    jQuery(".lb-button").click(function() {
        if('external' == jQuery(this).attr('rel'))
            window.open(jQuery(this).attr('data-url'), '_blank');
        else
            window.location = jQuery(this).attr('data-url');
    });
    jQuery(".lb-homepage-link").click(function() {
        if('external' == jQuery(this).attr('rel'))
            window.open(jQuery(this).attr('data-url'), '_blank');
        else
            window.location = jQuery(this).attr('data-url');
    });
});