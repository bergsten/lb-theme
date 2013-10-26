/* 
 * jQuery function to open external site link on button click.
 * From http://stackoverflow.com/questions/4944387/go-to-link-on-button-click-jquery/4944460#4944460
 */
jQuery(document).ready(function(){
    jQuery(".lb-button").click(function() {
        window.location = jQuery(this).attr('data-url');
    });
});