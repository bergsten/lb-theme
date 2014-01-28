<?php
/**
 * Page Template
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a page ('page' post_type) unless another page template overrules this one.
 * @link http://codex.wordpress.org/Pages
 *
 * @package WooFramework
 * @subpackage Template
 */

get_header();
?>
<style>
    #nav-container {
        margin-bottom: 0 !important;
        margin: 0 !important;
    }
</style>
    <!-- #content Starts -->
	<?php woo_content_before(); ?>
    <div id="frontpage-slider"><?php putRevSlider( "homepage" ) ?></div>
    <div id="content" class="col-full">
    
    	<div id="main-sidebar-container">    

            <!-- #main Starts -->
            <?php woo_main_before(); ?>
            <section id="main">                     
<?php
	woo_loop_before();
	
	if (have_posts()) { $count = 0;
		while (have_posts()) { the_post(); $count++;
			woo_get_template_part( 'content', 'frontpage' ); // Get the page content template file, contextually.
		}
	}
	
	woo_loop_after();
?>
<div id="3-col-section-promotion" style="text-align: center;">
<ul id="Grid" style="align: middle;">
	<li class="mix mix_all" style="display: inline-block; opacity: 1;" data-name="Smyckesguider">
            <div class="view view-tenth lb-item hover_effect">

                <img class="woo-image hover_effect" alt="Smyckesguider" src="http://lyxbling.se/wp-content/uploads/2013/09/diamanter-300x300.jpg" width="300" height="300" /> <span class="img-title">Smyckesguider</span>
                <div class="mask hover_effect">
                    <h2 class="hover_effect"><a href="http://lyxbling.se/smyckesguider">Smyckesguider</a></h2>
                    <p class="hover_effect">Lär dig mer om diamanter, ädelstenar, pärlor, ädelmetaller som guld, roséguld, platina, silver, stål, m.m. i vår smyckesguide...</p>
                    <div class="lb-button small" style="clear: both;" data-url="http://lyxbling.se/smyckesguider"><a href="http://lyxbling.se/smyckesguider">Läs mer »</a></div>
                </div>
            </div>
        </li>
	<li class="mix mix_all" style="display: inline-block; opacity: 1;" data-name="Smyckesvarumärken">
            <div class="view view-tenth lb-item hover_effect">

                <img class="woo-image hover_effect" alt="Smyckesvarumärken" src="http://lyxbling.se/wp-content/uploads/2013/08/mercedes-gp-petronas-thomas-sabo-300x200.jpg" width="300" height="300" /> <span class="img-title">Smyckesvarumärken</span>
                <div class="mask hover_effect">
                    <h2 class="hover_effect"><a href="http://lyxbling.se/smyckesvarumarken">Smyckesvarumärken</a></h2>
                    <p class="hover_effect">Läs om olika kända &amp; okända smyckesvarumärken och lär dig vilka designer som kännetecknar de olika smyckesvarumärkena...</p>
                    <div class="lb-button small" style="clear: both;" data-url="http://lyxbling.se/smyckesvarumarken"><a href="http://lyxbling.se/smyckesvarumarken">Läs mer »</a></div>
                </div>
            </div>
        </li>
	<li class="mix mix_all" style="display: inline-block; opacity: 1;" data-name="Smyckesbutiker">
            <div class="view view-tenth lb-item hover_effect">

                <img class="woo-image hover_effect" alt="Smyckesbutiker" src="http://lyxbling.se/wp-content/uploads/2014/01/pandorashop-onlinebutik-185x300.png" width="300" height="300" /> <span class="img-title">Smyckesbutiker</span>
                <div class="mask hover_effect">
                    <h2 class="hover_effect"><a href="http://lyxbling.se/smyckesbutiker">Smyckesbutiker</a></h2>
                    <p class="hover_effect">Vi granskar svenska &amp; utländska smyckesbutiker online - Du väljer var du vill shoppa...</p>
                    <div class="lb-button small" style="clear: both;" data-url="http://lyxbling.se/smyckesbutiker"><a href="http://lyxbling.se/smyckesbutiker">Läs mer »</a></div>
                </div>
            </div>
        </li>
</ul>
</div>
            </section><!-- /#main -->
            <?php woo_main_after(); ?>
    
            <?php get_sidebar(); ?>

		</div><!-- /#main-sidebar-container -->         

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php woo_content_after(); ?>

<?php get_footer(); ?>