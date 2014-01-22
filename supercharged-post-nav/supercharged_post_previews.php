<?php

// turn into a plugin

function post_preview() {
					
	$next_post = get_previous_post();
					
	if ($next_post) {
					
		$thumb_id = get_post_thumbnail_id($next_post->ID);
		$thumb_url = wp_get_attachment_image_src($thumb_id,'large', true);
					
		if ( has_excerpt( $next_post->ID ) ) {
			$the_excerpt = $next_post->post_excerpt;
		} else {
			$the_excerpt = substr( $next_post->post_content, 0, strpos( $next_post->post_content, '<!--more-->' ) );
		}
		
		
	// leave HTML in here
	// move CSS to own file or leave as is?	
						 
?>
					
	<div class="next-post-preview" style="padding: 30px; opacity:0.7; background-image: url(<?php echo $thumb_url[0]; ?>)">
					
		<a href="<?php echo get_permalink( $next_post->ID ) ?>">
					
			<div class="next-post-preview-content" style="width: 80%; margin: auto; background-color: #000; opacity: 0.8; padding: 20px; ">
						
			<div style="color: #fff; width: 100%; border-top: 2px solid #fff; margin-top: 20px; padding-top: 5px; text-transform: uppercase">> next post </div>
											
				<h2 style="color: #fff;"><?php echo $next_post->post_title; ?></h2>
						
				<div style="color: #fff; font-weight: bold;"><?php echo apply_filters( 'the_content', $the_excerpt ); ?></div>
						
			</div>
						
		</a>
					
	</div>
	
<?php

}

