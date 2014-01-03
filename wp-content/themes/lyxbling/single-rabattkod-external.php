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

include_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

status_header(200);
header("X-Robots-Tag: noindex, nofollow", true);

$post = get_post(get_query_var('postid'));

$store_id = get_post_meta($post->ID, 'wpcf-store-post-id', true);
$butik_post = lb_get_related_posts_by_taxonomy($post->ID, 'butik', 'smyckesbutiker');
$link_data = lb_get_link_data($butik_post->posts[0]->ID);
$target_url = $link_data['target_url'];

if(isset($link_data['affiliate_url']))
    $target_url = $link_data['affiliate_url'];

the_post(); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
        <title><?php echo($post->post_title); ?></title>
        <?php woo_meta(); ?>
        <?php wp_head(); ?>
        <?php woo_head(); ?>
    </head>
    <body id="rabattkod-body">
        <div id="rabattkod-header-container">
            <div id="rabattkod-header">
                <div id="logo" itemscope="" itemtype="http://schema.org/Organization">
                    <a href="http://lyxbling.se/" title="Om smycken &amp; accessoarer för dig som gillar smakfullt bling bling"><img itemprop="logo" src="http://lyxbling.se/wp-content/uploads/2013/10/lyxbling-logotyp.png" alt="LyxBling.se"></a>
                    <span class="site-title"><a itemprop="url" href="http://lyxbling.se/"><span itemprop="brand">LyxBling.se</span></a></span>
                    <span itemprop="description" class="site-description">Om smycken &amp; accessoarer för dig som gillar smakfullt bling bling</span>
                </div>
                <div id="rabattkod-container">
                    <h1 class="rabattkod-title"><?php echo $post->post_title; ?></h1><?php
                    if(isset($link_data['affiliate_impression_tracking']))
                        echo($link_data['affiliate_impression_tracking']);
                    ?>
                    <div class="rabattkod">Använd rabattkod "<?php echo(get_post_meta($post->ID, 'wpcf-rabattkod', true)); ?>"</div>
                </div>
                <div id="rabattkod-close-iframe">
                    <a href="http://lyxbling.se/till/<?php echo($post->ID); ?>?noiframe=true" title="Stäng sidhuvudet">&#x2717</a>
                </div>
            </div>
        </div>
    
        <iframe id="preview-frame" src="<?php echo($target_url); ?>" frameborder="0" noresize="noresize" ></iframe>
    </body>
</html>
