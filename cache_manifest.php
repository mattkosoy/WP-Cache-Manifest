<?php
/*
File Name:   cache_manifest.php
File Description:  this file works as the custom page template for our 'cache_manifest' page.  
Author: Matt Kosoy
Version: 0.1.1
Author URI: http://mattkosoy.com/
*/
header('Content-Type: text/cache-manifest'); // Add the correct Content-Type for the cache manifest
$query = new WP_Query( 'name=cache_manifest&post_type=page' ); // query for the cache_manifest page
?>
CACHE MANIFEST
<?php
while ( $query->have_posts() ) : $query->the_post();
	echo $post->post_content;
endwhile;
?>