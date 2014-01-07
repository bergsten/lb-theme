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
//Add Woocommerce body classes
function lb_body_classes($classes){
    foreach($classes as $key => $val) {
        if('three-col-left' == $val) {
            $classes[$key] = 'one-col';
        }
        if('three-col-left-1200' == $val) {
            $classes[$key] = 'one-col-1200';
        }
    }
    
    return $classes;
}
add_filter('body_class', 'lb_body_classes', 1000);

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
	get_template_part( 'loop', 'smyckesdesigners' );
?>
            </section><!-- /#main -->
            <?php woo_main_after(); ?>
	</div><!-- /#main-sidebar-container -->     
    </div><!-- /#content -->
	<?php woo_content_after(); ?>

<?php get_footer(); ?>