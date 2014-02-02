<?php
/**
 * Plugin Name: Supercharged Post Navigation
 * Plugin URI: http://premium.wpmudev.org/blog
 * Description: Provides a much enhanced post navigation via supercharged_post_navigation function
 * Version: 1.0
 * Author: Chris Knowles
 * Author URI: http://premium.wpmudev.org/blog/author/chrisdknowles
 * License: GPL2
 */

function supercharged_post_navigation( $title_text='> next post' , $image_size='post_thumbnail' ) {
					
	$in_same_term = true; // get posts from same category
	$excluded_terms = ''; // included all categories
	$previous = true; // get previous post - for date descending next post is previous				
	
	// get the "previous" post in same category				
	$next_post = get_adjacent_post( $in_same_term, $excluded_terms, $previous);
	
	// nothing found? try without category
	if ( !$next_post ) {
	
		$in_same_term = false; // get post regardless of category
		$next_post = get_adjacent_post( $in_same_term, $excluded_terms, $previous);
	}
					
	//got a post!				
	if ($next_post) {
		
		// get the featured image			
		$thumb_id = get_post_thumbnail_id($next_post->ID);
		$thumb_url = wp_get_attachment_image_src($thumb_id, $image_size , true);
		
		// get the excerpt - IMPORTANTL: You must use either the excerpt metabox or the <!--more--> tag		
		if ( has_excerpt( $next_post->ID ) ) {
			$the_excerpt = $next_post->post_excerpt;
		} else {
			$the_excerpt = substr( $next_post->post_content, 0, strpos( $next_post->post_content, '<!--more-->' ) );
		}
		
		echo '
		<div class="next-post-preview" style="padding: 30px; opacity:0.7; background-image: url(' . $thumb_url[0] . ')">
			<a href="' . get_permalink( $next_post->ID ) . '">
				<div class="next-post-preview-content" style="width: 80%; margin: auto; background-color: #000; opacity: 0.7; padding: 20px; ">					
					<div style="color: #fff; width: 100%; border-top: 2px solid #fff; margin-top: 20px; padding-top: 5px; text-transform: uppercase">' . $title_text . '</div>				
					<h2 style="color: #fff;">' . $next_post->post_title . '</h2>
					<div style="color: #fff; font-weight: bold;">' . apply_filters( 'the_content' , $the_excerpt ) . '</div> 
				</div>
			</a>	
		</div>';
		
	}
	
} // end function
