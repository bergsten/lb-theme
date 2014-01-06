<?php
/**
 * "Portfolio" Post Type Archive Template
 *
 * This template file is used when displaying an archive of posts of the
 * "portfolio" post type. This is used with WooTumblog.
 *
 * @package WooFramework
 * @subpackage Template
 */

 global $woo_options; 
 get_header();
?>
    <!-- #content Starts -->
	<?php woo_content_before(); ?>
    <div id="content" class="col-full">
    
    	<div id="main-sidebar-container">    

            <!-- #main Starts -->
            <?php woo_main_before(); ?>
            <section id="main"> 
<?php
	get_template_part( 'loop', 'smyckesvarumarken' );
?>
            </section><!-- /#main -->
            <?php woo_main_after(); ?>
	</div><!-- /#main-sidebar-container -->
    </div><!-- /#content -->
	<?php woo_content_after(); ?>

<?php get_footer(); ?>