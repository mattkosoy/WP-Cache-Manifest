<?php
/**
 * @package WP Cache Manifest
 * @version 0.1	
 */
/*
Plugin Name: WP Cache Manifest
Plugin URI: https://github.com/mattkosoy/wp-cache-manifest
Description: A tool to create a cache manifest file that will enable offline application support in mobile web browsers.
Author: Matt Kosoy
Version: 0.1
Author URI: http://mattkosoy.com/
*/


/*
	$post = array(
	  'ID' => null, 
	  'comment_status' => 'closed', 
	  'ping_status' => 'closed', 
	  'post_author' => $current_user->ID, 
	  'post_content' =>  $update->description,  
	  'menu_order' => $i++,
	  'post_date' => $update->upload_date, 
	  'post_date_gmt' => $update->upload_date, 
	  'post_excerpt' => $update->url, 
	  'post_name' => $update->title,
	  //'post_parent' =>$Video_parentPage, 
	  'post_status' => 'draft',  
	  'post_title' => addslashes($update->title),
	  'post_type' => 'Video',
	  'tags_input' => explode(',',$update->tags), 
	); 
	// go ahead and insert new page record to local db.
	$success = wp_insert_post( $post );
*/

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/* define vars & add plugin actions */

// hook into wp and add in the administration menus
if ( is_admin() ){ // admin actions
	add_action('admin_menu', '_create_menu');
    add_action('admin_init', '_register_settings' );
	
#	add_action('admin_head', '_update_videos_js');
}
/**
 * @function AA_register_settings
 * @descrip: registers our cache settings in the system
 */
function _register_settings() {
	register_setting( '_CacheManifestSettings', 'cached_content_types' ); 	// setting to select what content types to cache.
	register_setting( '_CacheManifestSettings', 'cache_enabled');		// setting to select wether or not to cache js files
	register_setting( '_CacheManifestSettings', 'cached_js_setting');		// setting to select wether or not to cache js files
	register_setting( '_CacheManifestSettings', 'cached_img_setting');		// setting for images
	register_setting( '_CacheManifestSettings', 'cached_css_setting');		// setting for css
	register_setting( '_CacheManifestSettings', 'cached_font_setting');		// setting for web fonts

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
	$cached_content_types = get_option('cached_content_types'); 
	$cache_enabled = get_option('cache_enabled'); 
	$cached_js_setting = get_option('cached_js_setting'); 
	$cached_img_setting = get_option('cached_img_setting'); 
	$cached_css_setting = get_option('cached_css_setting'); 
	$cached_font_setting = get_option('cached_font_setting'); 

	// to do:  update the UI to display current settings

?>
<div class="wrap">
<h2>Manage Cache Manifest Settings</h2>
<div class="updated below-h2" id="message" style="display:none;"></div>
<form method="post" action="options.php">
    <?php settings_fields( '_CacheManifestSettings' ); ?>
    <table class="form-table">
 
  		<!-- Cache This Site for Offline Viewing -->
        <tr valign="top">
        <th scope="row">
        	<h3>Enable Cache</h3>
        </th>
        <td style="padding-top: 30px;">
			<input type="checkbox" id="cache_enabled" value="yes" >
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
							echo '<option value="'.$pt.'">'.$pt.'</option>'."\n";
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
			<input type="checkbox" id="cached_js_setting" value="yes" >
			<input type="text" id="cached_js_folder_path" value="js" style="width:66%"/>
			<br/>
			<label for="cached_img_folder_path">Add the path to your theme's js directory</label>
        </td>
        </tr>
        
        <!-- Add CSS to the Cache file? -->
        <tr valign="top">
        <th scope="row">
        	<h3>Include CSS</h3>
        </th>
        <td style="padding-top: 30px;">
			<input type="checkbox" id="cached_css_setting" value="yes" >
			<input type="text" id="cached_css_folder_path" value="" style="width:66%"/>
			<br/>
			<label for="cached_img_folder_path">Add the path to your theme's css directory</label>
        </td>
        </tr>        
        
        <!-- Add Webfonts to the Cache file? -->
        <tr valign="top">
        <th scope="row">
        	<h3>Include Webfonts</h3>
        </th>
        <td style="padding-top: 30px;">
			<input type="checkbox" id="cached_font_setting" value="yes" >
			<input type="text" id="cached_font_folder_path" value="webfonts" style="width:66%"/>
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
			<input type="checkbox" id="cached_img_setting" value="yes" >
			<input type="text" id="cached_img_folder_path" value="img" style="width:66%"/>
			<br/>
			<label for="cached_img_folder_path">Add the path to your theme's images directory</label>
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
/* EOF */  ?>
