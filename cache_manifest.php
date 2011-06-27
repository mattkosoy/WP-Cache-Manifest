<?php
// Add the correct Content-Type for the cache manifest
header('Content-Type: text/cache-manifest');
$query = new WP_Query( 'name=cache_manifest&post_type=page' );
?>
CACHE MANIFEST
<?php
while ( $query->have_posts() ) : $query->the_post();
	echo $post->post_content;
endwhile;
?>