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
 * @descrip: registers our Video settings in the system
 */
function _register_settings() {
	register_setting( '_CacheManifestSettings', 'vimeo_username' );
	
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/**
 * @function _create_menu
 * @descrip: adds a submenu under the 'options' panel in wp-admin for managing 'Video settings'
 */
function _create_menu() {
    add_management_page('cache.manifest Settings', 'cache-manifest', 'administrator', 'cache-manifest', '_settings_page');
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/**
 * @function _settings_page
 * @descrip:admin page HTML for adding/editing cache manifest settings
*/
function _settings_page() { 
	$pages = get_posts(array('post_type'=>'page', 'post_parent'=>0));
	if(is_array($pages)){
		$page_options = array();
		foreach($pages as $page){
			if($page->ID == $Video_parentPage){ $s = 'selected="SELECTED"'; } else { $s = ''; }
			$page_options[] = '<option value="'.$page->ID.'" '.$s.' >'.$page->post_title.'</option>'."\n";
		}
	}
?>
<div class="wrap">
<h2>Manage Cache Manifest Settings</h2>
<div class="updated below-h2" id="message" style="display:none;"></div>
<form method="post" action="options.php">
    <?php settings_fields( '_CacheManifestSettings' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Video Username</th>
        <td><input type="text" name="vimeo_username" value="<?php echo $vimeo_username; ?>" /></td>
        </tr>
    </table>
    <p class="submit">
    <input type="submit"  class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
</div>

</div>
<?php } 

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/* EOF */  ?>
