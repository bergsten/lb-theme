/* 
 * jQuery function to open external site link on button click.
 * From http://stackoverflow.com/questions/4944387/go-to-link-on-button-click-jquery/4944460#4944460
 */
$('.button-external').click(function() {
    window.location = "www.example.com/index.php?id=" + this.id;
});

