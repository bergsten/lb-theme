<?php
/*  */ 

$settings = array(
				'thumb_w' => 150,
				'thumb_h' => 150,
				'thumb_align' => 'alignleft',
				'post_content' => 'excerpt',
				'comments' => 'both'
				);

$title_before = '<h1 class="title blingmasters entry-title">';
$title_after = '</h1>';

global $wpdb;

$authors = $wpdb->get_results("SELECT ID, user_nicename from $wpdb->users ORDER BY display_name");

woo_post_before();
?>
<article <?php post_class(); ?>>
<?php
woo_post_inside_before();
?>
	<header>
	<?php the_title( $title_before, $title_after ); ?>
	</header>
        
	<section class="entry">
<?php
// Show the content
the_content(); 

foreach($authors as $author) { 
    $user_data = get_userdata( $author->ID );
//pr($user_data);
    if( $user_data->allcaps['publish_posts'] && !$user_data->allcaps['administrator'] ) { ?>
        <aside id="post-author">
            <div class="profile-image"><?php echo(get_avatar($author->ID, $size = '80')); ?></div>
            <div class="profile-content">
                    <h4><a href="<?php echo( $user_data->data->user_url ); ?>"><?php echo( $user_data->data->display_name ); ?></a></h4>
                    <p><?php echo($user_data->user_description); ?></p>
            </div>
            <div class="fix"></div>
        </aside>
        <?php
    }
}

if ( 'content' == $settings['post_content'] || is_singular() ) wp_link_pages( $page_link_args );
?>
	</section><!-- /.entry -->
	<div class="fix"></div>
<?php
woo_post_inside_after();
?>
</article><!-- /.post -->
<?php
woo_post_after();
$comm = $settings['comments'];
if ( ( 'post' == $comm || 'both' == $comm ) && is_single() ) { comments_template(); }
?>