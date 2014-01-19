<?php
/**
 * Plugin Name: Partial
 * Plugin URI: http://twentyfourteen.dev
 * Description: Return HTML for object only
 * Version: 1.0
 * Author: Chris Knowles
 * Author URI: http://twentyfourteen.dev
 * License: GPL2
 */

function partial_add_endpoint() {
    add_rewrite_endpoint( 'partial', EP_PERMALINK );
}
add_action( 'init', 'partial_add_endpoint' );

function partial_template_redirect() {
    global $wp_query;
 
    // if this is not a request for partial or a singular object then bail
    if ( ! isset( $wp_query->query_vars['partial'] ) || ! is_singular() )
        return;
 
	// include custom template
    include dirname( __FILE__ ) . '/partial-template.php';

    exit;
}

add_action( 'template_redirect', 'partial_template_redirect' );

function partial_endpoints_activate() {

    // ensure our endpoint is added before flushing rewrite rules
    partial_add_endpoint();
    // flush rewrite rules - only do this on activation as anything more frequent is bad!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'partial_endpoints_activate' );
 
function partial_endpoints_deactivate() {
    // flush rules on deactivate as well so they're not left hanging around uselessly
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'partial_endpoints_deactivate' );