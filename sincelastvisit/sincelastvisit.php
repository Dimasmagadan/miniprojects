<?php
/**
 * Plugin Name: Since Last Visit
 * Plugin URI: http://premium.wpmudev.org
 * Description: Adds a "tag" to the title of posts published since visitor's last visit. Only works for initial page
 * Version: 1.0
 * Author: Chris Knowles
 * Author URI: http://twitter.com/ChrisKnowles
 * License: GPL2
 */
 
/*
 *  When the title is output check if the post was published after
 *  the last visit date
 */
function sincelastvisit_the_title ( $title, $id ) {
 
 	// if not in the loop then don't worry.
 	if ( !in_the_loop() || is_singular() || get_post_type( $id ) == 'page' ) return $title;
 
	// if no cookie then just return the title 
 	if ( !isset($_COOKIE['lastvisit']) ||  $_COOKIE['lastvisit'] == '' ) return $title;
 
 	// get the date of the last visit from the cookie
 	$lastvisit = $_COOKIE['lastvisit'];
 	
 	// get the publish date of the post in UNIX GMT
 	$publish_date = get_post_time( 'U', true, $id );
 	
 	// if published since last visit then add the "new" tag
 	if ($publish_date > $lastvisit) $title .= '<span class="nslv">new</span>';
 	
 	return $title;
 
}
 
add_filter( 'the_title', 'sincelastvisit_the_title', 10, 2);
 
/*
 *  Set a cookie for lastvisit with current GMT datetime in UNIX format
 */
function sincelastvisit_set_cookie() {
 	
 	if ( is_admin() ) return;
 	
 	$current = current_time( 'timestamp', 1);
 	
 	// set the cookie with current datetime (UNIX format GMT), expires in 7 days
 	setcookie( 'lastvisit', $current, time()+60+60*24*7, COOKIEPATH, COOKIE_DOMAIN );
 
}
 
add_action( 'init', 'sincelastvisit_set_cookie' );