<?php
/*
Plugin Name: Multipost Tagger 
Plugin URI: 
Description: Add the same tag to multiple posts in the post list screen
Author: Chris Knowles
Author URI: http://premium.wpmudev.org
Version: 0.1
Original URL: http://www.foxrunsoftware.net/articles/wordpress/add-custom-bulk-action/
Original Author: Justin Stern
Original Author URI: http://www.foxrunsoftware.net
	
Original Licence Info: 	
	Copyright: © 2012 Justin Stern (email : justin@foxrunsoftware.net)
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
			
if ( is_admin() ) {
	add_action('admin_footer-edit.php' , 'custom_bulk_admin_footer' );
	add_action('load-edit.php' , 'custom_bulk_action' );
	add_action('admin_notices', 'custom_bulk_admin_notices' );
}
	
/**
* Step 1: add the custom Bulk Action to the select menus
*/
function custom_bulk_admin_footer() {

	global $post_type;
			
	if($post_type == 'post') {
		?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('<option>').val('addtags').text('<?php _e('Add Tags')?>').appendTo("select[name='action']");
					jQuery('<option>').val('addtags').text('<?php _e('Add Tags')?>').appendTo("select[name='action2']");
					jQuery('<option>').val('removetags').text('<?php _e('Remove Tags')?>').appendTo("select[name='action']");
					jQuery('<option>').val('removetags').text('<?php _e('Remove Tags')?>').appendTo("select[name='action2']");
					jQuery('div.bulkactions:first').append('<span>Tags: </span><input type="text" name="action_tags" value="">');
				});
			</script>
		<?php
	}
}
		
/**
* Step 2: handle the custom Bulk Action
* 
* Based on the post http://wordpress.stackexchange.com/questions/29822/custom-bulk-action
*/
function custom_bulk_action() {

	global $typenow;
	
	$post_type = $typenow;
			
	if($post_type == 'post') {
				
		// get the action
		$wp_list_table = _get_list_table('WP_Posts_List_Table');  // depending on your resource type this could be WP_Users_List_Table, WP_Comments_List_Table, etc
		$action = $wp_list_table->current_action();
				
		$allowed_actions = array("addtags" , "removetags" );
		
		if(!in_array($action, $allowed_actions)) return;
				
		// security check
		check_admin_referer('bulk-posts');

		if (!isset($_REQUEST['action_tags'])) return;
		
		$tags = $_REQUEST['action_tags'];
				
		// make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
		if(isset($_REQUEST['post'])) {
			$post_ids = array_map('intval', $_REQUEST['post']);
		}
		
		// no ids selected		
		if(empty($post_ids)) return;
				
		// this is based on wp-admin/edit.php
		$sendback = remove_query_arg( array('tagged', 'untrashed', 'deleted', 'ids'), wp_get_referer() );
		if ( ! $sendback )
			$sendback = admin_url( "edit.php?post_type=$post_type" );
				
		$pagenum = $wp_list_table->get_pagenum();
		$sendback = add_query_arg( 'paged', $pagenum, $sendback );
				
		switch($action) {
			case 'addtags':
						
				$tagged = 0;
				foreach( $post_ids as $post_id ) {
							
					if ( add_tags( $post_id , $tags ) ) $tagged++;
					
				}
						
				$sendback = add_query_arg( array('tagged' => $tagged, 'ids' => join(',', $post_ids) ), $sendback );
				break;

			case 'removetags':
						
				$tagged = 0;
				foreach( $post_ids as $post_id ) {
							
					if ( remove_tags( $post_id , $tags ) ) $tagged++;
					
				}
						
				$sendback = add_query_arg( array('tagged' => $tagged, 'ids' => join(',', $post_ids) ), $sendback );
				break;

					
			default: return;
		}
				
		$sendback = remove_query_arg( array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );
				
		wp_redirect($sendback);
		exit();
	} //if
}
		
/**
* Step 3: display an admin notice on the Posts page after exporting
*/
function custom_bulk_admin_notices() {

	global $post_type, $pagenow;
			
	if($pagenow == 'edit.php' && $post_type == 'post' && isset($_REQUEST['tagged']) && (int) $_REQUEST['tagged']) {
		$message = sprintf( _n( 'Post updated.', '%s posts updated.', (int) $_REQUEST['tagged'] ), number_format_i18n( $_REQUEST['tagged'] ) );
		echo "<div class=\"updated\"><p>{$message}</p></div>";
	}
}
		
/**
* Step 4: perform the tagging
*/
function add_tags( $post_id, $tags ) {
	
	return wp_set_post_terms( $post_id, $tags, 'post_tag', true);
}


function remove_tags( $post_id, $tags ) {

	// as there is no function to remove tags, we need to make a list of current tags, 
	// remove the passed tags from the list and then reapply

	// turn $tags into an array
	$tag_list = explode( ',', trim( $tags, " \n\t\r\0\x0B," ) );
	
	// get current tags
	$post_tags = wp_get_post_terms( $post_id, 'post_tag' , array("fields" => "names") );
	
 	//echo '<br/>tag_list ' . print_r($tag_list, true);
 	//echo '<br/>post_tags before ' . print_r($post_tags, true);	

	// remove passed tags from current tags
	$post_tags = array_diff( $post_tags, $tag_list );
	
 	//echo '<br/>post_tags after ' . print_r($post_tags, true); 

	// assign new list
	$result = wp_set_post_terms( $post_id, $post_tags, 'post_tag', false);
	
	if (is_array( $result ) ) {
		return true;
	} else {
		return false;
	}
		
}
