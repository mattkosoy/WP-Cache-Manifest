<?php
/*
Plugin Name: WP Cache Manifest
Plugin URI: https://github.com/mattkosoy/wp-cache-manifest
File Name:   index.php
Description: A tool to create a cache manifest file that will enable offline application support in mobile web browsers.
Author: Matt Kosoy
Version: 0.1.2
Author URI: http://mattkosoy.com/
*/


global $wp_version;
if( version_compare( $wp_version, "3.1", "<" ) ){
    exit( 'This plugin requires WordPress 3.1 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>' );
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/* define vars & add plugin actions */


// hook into wp and add in the administration menus
if ( is_admin() ){ // admin actions
	add_action('admin_menu', '_create_menu');
    add_action('admin_init', '_register_settings' );	
    add_action('admin_init', '_cache_manifest');
}

add_filter('page_template', '_cache_template');

/**
 * @function AA_register_settings
 * @descrip: registers our cache settings in the system
 */
function _register_settings() {
	register_setting( '_CacheManifestSettings', 'cached_content_types' ); 	// setting to select what content types to cache.
	register_setting( '_CacheManifestSettings', 'cache_enabled');		// setting to select wether or not to cache js files
	register_setting( '_CacheManifestSettings', 'cached_js_setting');		// setting to select wether or not to cache js files
	register_setting( '_CacheManifestSettings', 'cached_jquery_setting');		// setting to select wether or not to cache jquery from google's CDN
	register_setting( '_CacheManifestSettings', 'cached_img_setting');		// setting for images
	register_setting( '_CacheManifestSettings', 'cached_uploads_setting');		// setting for images
	register_setting( '_CacheManifestSettings', 'cached_css_setting');		// setting for css
	register_setting( '_CacheManifestSettings', 'cached_font_setting');		// setting for web fonts
	register_setting( '_CacheManifestSettings', 'cached_js_folder_path');		
	register_setting( '_CacheManifestSettings', 'cached_css_folder_path');		
	register_setting( '_CacheManifestSettings', 'cached_font_folder_path');		
	register_setting( '_CacheManifestSettings', 'cached_img_folder_path');		
	register_setting( '_CacheManifestSettings', 'cached_additional_urls');		
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/**
 * @function _create_menu
 * @descrip: adds a submenu under the 'options' panel in wp-admin for managing 'Video settings'
 */
function _create_menu() {
    add_options_page('Offline Content', 'Offline Content', 'administrator', 'cache-manifest', '_settings_page');
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/**
 * @function _settings_page
 * @descrip:admin page HTML for adding/editing cache manifest settings
*/
function _settings_page() { 
	/* get vars for content displayage */
	$cached_content_types = get_option('cached_content_types'); 
	$cache_enabled = get_option('cache_enabled'); 
	$cached_js_setting = get_option('cached_js_setting'); 
	$cached_jquery_setting = get_option('cached_jquery_setting'); 
	$cached_img_setting = get_option('cached_img_setting');
	$cached_uploads_setting = get_option('cached_uploads_setting'); 	
	$cached_css_setting = get_option('cached_css_setting'); 
	$cached_font_setting = get_option('cached_font_setting'); 
	$cached_js_folder_path = get_option('cached_js_folder_path'); 
	$cached_css_folder_path = get_option('cached_css_folder_path'); 
	$cached_font_folder_path = get_option('cached_font_folder_path'); 
	$cached_img_folder_path = get_option('cached_img_folder_path'); 
	$cached_additional_urls = get_option('cached_additional_urls'); 



/*
 * @ Uncomment the code below if you want this plugin to attempt to append -
 * @ the text/cache-manifest mime type to your .htaccess file
*/

/*

	// Todo:  Clean this up and make it so that it doesn't use fopen every time.
	
	
	// determine if we can write to the site's .htaccess file.  if we can add the mime type for cache manifest.  
	// if not, then display a message that tells the user to update their .htaccess file.
	$filename = ABSPATH.'.htaccess';
	if (is_writable($filename)) {
		$to_add = "\n\n# Add the cache manifest mimetype \nAddType text/cache-manifest .appcache";
		$file = file_get_contents($filename);
		if(!strpos($file, $to_add)) {
		  if (!$handle = fopen($filename, 'a')) {
				 echo "Cannot open file ($filename)";
				 exit;
			}
			// Write $somecontent to our opened file.
			if (fwrite($handle, $to_add) === FALSE) {
				echo "Cannot write to file ($filename)";
				exit;
			}
		} 
		$display_add_mime_message = false;
	} else {
		$display_add_mime_message = true;
	}

*/	
?>
<div class="wrap">
<h2>Manage Cache Manifest Settings</h2>
<div class="updated below-h2" id="message" style="display:none;"></div>
<form method="post" action="options.php">
    <?php settings_fields( '_CacheManifestSettings' ); ?>
    <table class="form-table">
 		<?php
		if($display_add_mime_message){ ?>
        <tr valign="top">
        <th scope="row">
        	<h3>.htaccess error!</h3>
        </th>
        <td style="padding-top: 30px;">
			Please add the following to your website's .htaccess file:
			<code><?php echo $to_add; ?></code>
        </td>
        </tr>
		<?php } ?>
  		<!-- Cache This Site for Offline Viewing -->
        <tr valign="top">
        <th scope="row">
        	<h3>Enable Cache</h3>
        </th>
        <td style="padding-top: 30px;">
        	<?php if($cache_enabled == 'yes') { $s = "checked"; } else { $s = ''; }?>
			<input type="checkbox" id="cache_enabled" name="cache_enabled" value="yes" <?php echo $s; ?>>
			<p> Be sure to update your theme's header.php file to include the manifest:
				<br/>
				<code>&lt;html manifest="&lt;?php bloginfo('home'); ?&gt;/cache_manifest"&gt;</code>
			</p>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">
        	<h3>Choose Content Types</h3>
        	<p>Select types you want to cache</p>
        </th>
        <td>
		<?php
					$post_types = get_post_types();
        ?>
        	<select multiple="multiple" size="<?php echo (count($post_types) - 2); ?>" name="cached_content_types[]" id="cached_content_types[]">
				<?php
					foreach($post_types as $pt){
						
						if($pt == 'revision' || $pt == 'nav_menu_item'){ } else {
							if(in_array($pt, $cached_content_types)){ $s = 'selected="selected"'; } else { $s = ''; }
							echo '<option value="'.$pt.'" '.$s.' >'.$pt.'</option>'."\n";
						}
					}
				?>
        	</select>
        </td>
        </tr>
        
        <!-- Add JS to the Cache file? -->
                <tr valign="top">
        <th scope="row">
        	<h3>Include Javascript</h3>
        </th>
        <td style="padding-top: 30px;">
        	<?php if($cached_js_setting == 'yes') { $s = "checked"; } else { $s = ''; }?>
        
			<input type="checkbox" id="cached_js_setting" name="cached_js_setting" value="yes" <?php echo $s; ?> >
			<input type="text" id="cached_js_folder_path" name="cached_js_folder_path" value="<? echo $cached_js_folder_path; ?>" style="width:66%"/>
			<br/>
			<label for="cached_js_folder_path">Add the path to your theme's js directory</label>
        </td>
        </tr>
        
        <!-- Add JS to the Cache file? -->
        <tr valign="top">
        <th scope="row">
        	<h3>Include Jquery</h3>
        </th>
        <td style="padding-top: 30px;">
        	<?php if($cached_jquery_setting == 'yes') { $s = "checked"; } else { $s = ''; }?>
        
			<input type="checkbox" id="cached_jquery_setting" name="cached_jquery_setting" value="yes" <?php echo $s; ?> >
			<label for="cached_jquery_folder_path">Selecting this option will include a reference to jQuery via the Google CDN</label>
        </td>
        </tr>
        
        
        <!-- Add CSS to the Cache file? -->
        <tr valign="top">
        <th scope="row">
        	<h3>Include CSS</h3>
        </th>
        <td style="padding-top: 30px;">
        	<?php if($cached_css_setting == 'yes') { $s = "checked"; } else { $s = ''; }?>
			<input type="checkbox" id="cached_css_setting" name="cached_css_setting" value="yes" <? echo $s; ?> >
			<input type="text" id="cached_css_folder_path" name="cached_css_folder_path" value="<? echo $cached_css_folder_path; ?>" style="width:66%"/>
			<br/>
			<label for="cached_css_folder_path">Add the path to your theme's css directory</label>
        </td>
        </tr>        
        
        <!-- Add Webfonts to the Cache file? -->
        <tr valign="top">
        <th scope="row">
        	<h3>Include Webfonts</h3>
        </th>
        <td style="padding-top: 30px;">
        	<?php if($cached_font_setting == 'yes') { $s = "checked"; } else { $s = ''; }?>
        
			<input type="checkbox" id="cached_font_setting" name="cached_font_setting" value="yes" <? echo $s; ?>>
			<input type="text" id="cached_font_folder_path" name="cached_font_folder_path" value="<? echo $cached_font_folder_path; ?>" style="width:66%"/>
			<br/>
			<label for="cached_font_folder_path">Add the path to your theme's fonts directory</label>
        </td>
        </tr>          
        
         <!-- Add Theme Images to the Cache file? -->
        <tr valign="top">
        <th scope="row">
        	<h3>Include Images</h3>
        </th>
        <td style="padding-top: 30px;">
        	<?php if($cached_img_setting == 'yes') { $s = "checked"; } else { $s = ''; }?>
        
			<input type="checkbox" id="cached_img_setting" name="cached_img_setting" value="yes" <? echo $s; ?>>
			<input type="text" id="cached_img_folder_path" name="cached_img_folder_path" value="<? echo $cached_img_folder_path; ?>" style="width:66%"/>
			<br/>
			<label for="cached_img_folder_path">Add the path to your theme's images directory</label>
        </td>
        </tr> 

    <!-- Add Theme Images to the Cache file? -->
        <tr valign="top">
        <th scope="row">
        	<h3>Include Uploads Directory</h3>
        </th>
        <td style="padding-top: 30px;">
        	<?php if($cached_uploads_setting == 'yes') { $s = "checked"; } else { $s = ''; }?>
			<input type="checkbox" id="cached_uploads_setting" name="cached_uploads_setting" value="yes" <? echo $s; ?>>
		</td>
		</tr>


         <!-- Add Additional URLs -->
        <tr valign="top">
        <th scope="row">
        	<h3>Additional URLs</h3>
        </th>
        <td style="padding-top: 30px;">
			<textarea id="cached_additional_urls" name="cached_additional_urls" style="width:85%; height:200px; clear:both;"><? echo $cached_additional_urls; ?></textarea>
			<br/>
			<label for="cached_additional_urls">Add additionlal URLs to the cache file here.</label>
        </td>
        </tr> 
        
    </table>
    <p class="submit">
    <input type="submit"  class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
</div>
</div>

<style type="text/css">
	#wpcontent select {
		height:auto;
	}
</style>

<script type="text/javascript">
</script>
<?php } 
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/**
 * @function _settings_page
 * @descrip:admin page HTML for adding/editing cache manifest settings
*/
function _cache_manifest() { 
	global $user_ID;
	/* get vars for content displayage */
	$cached_content_types = get_option('cached_content_types'); 
	$cache_enabled = get_option('cache_enabled'); 
	$cached_js_setting = get_option('cached_js_setting'); 
	$cached_jquery_setting = get_option('cached_jquery_setting'); 
	$cached_img_setting = get_option('cached_img_setting'); 
	$cached_uploads_setting = get_option('cached_uploads_setting'); 
	$cached_css_setting = get_option('cached_css_setting'); 
	$cached_font_setting = get_option('cached_font_setting'); 
	$cached_js_folder_path = get_option('cached_js_folder_path'); 
	$cached_css_folder_path = get_option('cached_css_folder_path'); 
	$cached_font_folder_path = get_option('cached_font_folder_path'); 
	$cached_img_folder_path = get_option('cached_img_folder_path'); 
	$cached_additional_urls = get_option('cached_additional_urls'); 

	// set up the post content for our cache manifest
#	$cache_manifest_content = "# Website root \n"; // start w/ the index of our website.	
#	$cache_manifest_content.= get_bloginfo('home')."\n";

	// Javascript
	if($cached_js_setting == 'yes'){
		$cache_manifest_content.= "# Javascript \n";
		$cache_manifest_content.= get_bloginfo('template_url')."/".$cached_js_folder_path."/\n";
		$cache_manifest_content.= recurseDirectories(TEMPLATEPATH."/".$cached_js_folder_path);
	} 
	// jQuery
	if($cached_js_setting == 'yes'){
		$cache_manifest_content.= "# jQuery \n";
		$cache_manifest_content.= "https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js\n";
	} 

	// CSS
	if($cached_css_setting == 'yes'){
		$cache_manifest_content.= "# CSS \n";
		if($cached_css_folder_path == ''){
			$cache_manifest_content.= get_bloginfo('stylesheet_url')."\n";
		} else {
			$cache_manifest_content.= get_bloginfo('template_url')."/".$cached_css_folder_path."/\n";
			$cache_manifest_content.= recurseDirectories(TEMPLATEPATH."/".$cached_css_folder_path);
		}
	} 

	// Webfonts
	if($cached_font_setting == 'yes'){
		$cache_manifest_content.= "# Custom font directory \n";
		$cache_manifest_content.= get_bloginfo('template_url')."/".$cached_font_folder_path."/\n";
		$cache_manifest_content.= recurseDirectories(TEMPLATEPATH."/".$cached_font_folder_path);
	}

	// images
	if($cached_img_setting == 'yes'){
		// theme image files
		$cache_manifest_content.= "# Theme image files \n";
		$cache_manifest_content.= get_bloginfo('template_url')."/".$cached_img_folder_path."/\n";
		$cache_manifest_content.= recurseDirectories(TEMPLATEPATH."/".$cached_img_folder_path);
	}

	//  uploads
	if($cached_uploads_setting == 'yes'){
		$upload_dir = wp_upload_dir();
		$cache_manifest_content.= "# Uploads directory \n";
		$cache_manifest_content.= $upload_dir['baseurl']."/\n";
		$cache_manifest_content.= recurseDirectories($upload_dir['path']);
	}
	
	// post types	
	if(count($cached_content_types) > 0 && is_array($cached_content_types)){
		foreach($cached_content_types as $c){
			if($c != ''){ 
				$p = get_posts('post_type='.$c.'&numberposts=-1');
				if(count($p) >0){ // if we have found posts from this post type then add the guid's to our cache manifest file.
					$cache_manifest_content .= "# ".ucwords($c). " Post Type \n";
					foreach($p as $x){
						if($x->post_name != 'cache_manifest'){ 
							if($c != 'attachment'){ 
								$cache_manifest_content .= get_permalink($x->ID)."/\n";
							} else {
								$cache_manifest_content .= $x->guid."\n";
							}
						}
					}
				}
			}
		}
	}

	// additional schmutz
	$cache_manifest_content.= "# Etc \n";
	$cache_manifest_content.= $cached_additional_urls."\n";

	// test to see if we've already added a cache manifest post to this instance of wordpress.
	$query = new WP_Query( 'name=cache_manifest&post_type=page' );
	if($query->have_posts()){	
		$post = $query->posts[0];		// if we have added an instance, then assign it to the plugin variable.
		if($cache_enabled == 'yes'){
			$post->post_status = 'publish';
		} else {
			$post->post_status = 'draft';
		}
		$post->post_content = '';
		$post->post_content = $cache_manifest_content;
	} else { 
		$post = array(					//  if not, then create an instance.
		  'ID' => null, 
		  'comment_status' => 'closed', 
		  'ping_status' => 'closed', 
		  'post_author' => $user_ID, 
		  'post_date' => date('Y-m-d H:i:s'), 
		  'post_date_gmt' => date('Y-m-d H:i:s'), 
		  'post_name' => 'cache_manifest',
		  'post_parent' => 0, 
		  'post_title' => 'Cache Manifest',
		  'post_type' => 'page',
		  'guid' =>  get_bloginfo('home').'/cache_manifest',
		  'post_mime_type' => 'text/cache-manifest'
		); 
		if($cache_enabled == 'yes'){
			$post['post_status'] = 'publish';
		} else {
			$post['post_status'] = 'draft';
		}		
		$post['post_content'] = $cache_manifest_content;
	}
	// go ahead and insert new page record to local db.
	$success = wp_update_post( $post );
	if( !$success ){
		wp_die('Error creating cache manifest');
	} else {
		update_post_meta( $success, '_wp_page_template', 'cache_manifest.php' ); // tell our post to use the cache_manifest template.
	} 
	// cache rules everything around me.
	// http://awesomedudesprinting.com/shop/cache-rules-everything-around-me-shirt/
}

// Page template filter callback
// 
function _cache_template($template) {
    // If tp-file.php is the set template
    if( is_page_template('cache_manifest.php') ){
        // Update path(must be path, use WP_PLUGIN_DIR and not WP_PLUGIN_URL) 
        $template = WP_PLUGIN_DIR . '/cache-manifest/cache_manifest.php';
 	}
 // Return
    return $template;
}

// recurse the directories and append their info to our cache manifest string.
// based on: http://snipplr.com/view.php?codeview&id=29761
function recurseDirectories($path){
	$dir = new RecursiveDirectoryIterator($path);
	$hashes = "";
	$to_return = '';
	foreach(new RecursiveIteratorIterator($dir) as $file) {
		if ($file->IsFile() &&
		$file != "./manifest.php" &&
		substr($file->getFilename(), 0, 1) != "." &&
		substr($file, 0, 9) != "./archive" &&
		strpos($file, "/.svn") === false) {
			$sanitized_file = str_replace ($_SERVER['DOCUMENT_ROOT'], '', $file);
			$to_return.= $sanitized_file . "\n";
			$hashes .= md5_file($file);
		}
	}
	$to_return.= "# Hash: " . md5($hashes) . "\n";
	return $to_return;
}

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/* EOF */  ?>
