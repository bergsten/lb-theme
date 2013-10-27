<?php
/**
 * Single Post Template
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a post ('post' post_type).
 * @link http://codex.wordpress.org/Post_Types#Post
 *
 * @package WooFramework
 * @subpackage Template
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
        <title><?php wp_title(''); ?></title>
        <?php woo_meta(); ?>
        <?php // Add the Font Awesome shit! - http://fortawesome.github.io/Font-Awesome/ ?>
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
        <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
        <link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>" />
        <?php wp_head(); ?>
        <?php woo_head();
        $slug = get_query_var('lb_external_url_page_slug');
        $post = get_post(); ?>
    </head>
    <body>
        <div id="iframe-header">
            <h1>LyxBling.se - <?php echo($slug); ?></h1>
            <p>Slug: <?php echo $post->post_name; ?></p>
            <?php pr($post); ?>
        </div>
        <iframe id="preview-frame" src="http://www.w3schools.com" frameborder="0" noresize="noresize" ></iframe>
    </body>
</html>