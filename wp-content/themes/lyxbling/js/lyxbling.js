/* 
 * jQuery function to open external site link on button click.
 * From http://stackoverflow.com/questions/4944387/go-to-link-on-button-click-jquery/4944460#4944460
 */
// Make the whole .lb-item div link to respective item page.
jQuery(document).on('click', ".lb-item", function() {
    event.preventDefault();
    
    if(undefined !== jQuery(this).attr('data-url')) {
        if('external' === jQuery(this).attr('rel'))
            window.open(jQuery(this).attr('data-url'), '_blank');
        else
            window.location = jQuery(this).attr('data-url');
    }
});

jQuery(document).ready(function(){
    jQuery(".lb-button").click(function() {
        if('external' === jQuery(this).attr('rel'))
            window.open(jQuery(this).attr('data-url'), '_blank');
        else if('nolink' === jQuery(this).attr('rel'))
            jQuery.noop();
        else
            window.location = jQuery(this).attr('data-url');
    });
    jQuery(".lb-homepage-link").click(function() {
        if('external' === jQuery(this).attr('rel'))
            window.open(jQuery(this).attr('data-url'), '_blank');
        else
            window.location = jQuery(this).attr('data-url');
    });
    /*
    // disable :hover on touch devices
    // based on https://gist.github.com/4404503 
    // via https://twitter.com/javan/status/284873379062890496
    // + https://twitter.com/pennig/status/285790598642946048
    // re http://retrogamecrunch.com/tmp/hover
    if ('createTouch' in document)
    {
        try
        {
            var ignore = /:hover/;
            for (var i=0; i<document.styleSheets.length; i++)
            {
                var sheet = document.styleSheets[i];
                for (var j=sheet.cssRules.length-1; j>=0; j--)
                {
                    var rule = sheet.cssRules[j];
                    if (rule.type === CSSRule.STYLE_RULE && ignore.test(rule.selectorText))
                    {
                        sheet.deleteRule(j);
                    }
                }
            }
        }
        catch(e){}
    }*/
    // Enable hover stuff for touch devices as well. See http://stackoverflow.com/questions/2851663/how-do-i-simulate-a-hover-with-a-touch-in-touch-enabled-browsers
    /*document.addEventListener("touchstart", function(){
        e.preventDefault();
        jQuery(this).toggleClass('hover_effect');
    }, true);*/
    /*jQuery('.hover').bind('touchstart touchend', function(e) {
        e.preventDefault();
        jQuery(this).toggleClass('hover_effect');
    });*/
    
    // From http://websolstore.com/how-to-add-pinterest-to-social-media-icons-in-prettyphoto/
    jQuery(".prettyPhoto").prettyPhoto({
        changepicturecallback: function onPictureChanged() {
            var href= "http://pinterest.com/pin/create/button/?url=" + encodeURIComponent(location.href.replace(location.hash, '')) + "&media=" + jQuery("#fullResImage").attr("src");
            jQuery(".pp_social").append('<div class="pinterest" ><a href=' + href + ' class="pin-it-button" count-layout="horizontal" target="_blank"><img border="0" src="http://assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>');
        }
    });
    
    jQuery(function() {
        jQuery("#Grid").mixitup({
            targetSelector: '.mix',
            filterSelector: '.filter',
            sortSelector: '.sort',
            buttonEvent: 'click',
            effects: ['fade','scale'],
            listEffects: null,
            easing: 'smooth',
            layoutMode: 'grid',
            targetDisplayGrid: 'inline-block',
            targetDisplayList: 'block',
            gridClass: '',
            listClass: '',
            transitionSpeed: 600,
            showOnLoad: 'all',
            sortOnLoad: ['data-name', 'desc'],
            multiFilter: false,
            filterLogic: 'or',
            resizeContainer: true,
            minHeight: 0,
            failClass: 'fail',
            perspectiveDistance: '3000',
            perspectiveOrigin: '50% 50%',
            animateGridList: true,
            onMixLoad: null,
            onMixStart: null,
            onMixEnd: null
        });
    });
});

// Facebook button.
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/sv_SE/all.js#xfbml=1&appId=156355774556315";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// Google plus one button.
window.___gcfg = {
  lang: 'sv',
  parsetags: 'onload'
};
(function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js?onload=onLoadCallback';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
