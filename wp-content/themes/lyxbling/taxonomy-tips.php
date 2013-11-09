<?php
/**
 * "Tävlingar" Taxonomy Archive Template
 *
 * This template file is used when displaying an archive of posts in the
 * "butik" taxonomy.
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
	get_template_part( 'loop', 'presenttips' );
?>
            </section><!-- /#main -->
            <?php woo_main_after(); ?>
    
            <?php get_sidebar(); ?>

		</div><!-- /#main-sidebar-container -->         

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php woo_content_after(); ?>

<?php get_footer(); ?>