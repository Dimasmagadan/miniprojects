
var comments_container = 'div#comments';
var content_container = 'div#content';

jQuery.noConflict();

jQuery(document).ready(function() {

	// don't do this if looking for comments
	if (window.location.href.indexOf( '#comments' ) > -1) return;

	jQuery( comments_container ).hide();
	initialise_Scrollspy();
	initialise_history();

});


function initialise_history(){

	// Bind to StateChange Event
    History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
        var State = History.getState(); // Note: We are using History.getState() instead of event.state
    });
}

function initialise_Scrollspy(){

	// spy on a links with rel="prev" - this does the autoloading;
	jQuery('a[rel="prev"]').on('scrollSpy:enter', doAutoLoad );
	jQuery('a[rel="prev"]').scrollSpy();

}

function doAutoLoad(){

	// grab the url for the new post
	var post_url = jQuery(this).attr('href');
	
	// check to see if pretty permalinks, if not then add partial=1
	if ( post_url.indexOf( '?p=' ) > -1 ) {	
		np_url = post_url + '&partial=1'
	} else {
		np_url = post_url + '/partial';
	}
			
	// remove the post navigation HTML
	jQuery('nav.post-navigation').remove();

	jQuery.get( np_url , function( data ) { 
	
		var $post_html = jQuery( '<hr class="post-divider" />' +	data ); 
		
		jQuery( content_container ).append( $post_html );
		
		// update the browser location
		History.pushState(null, null, post_url);
		 	
		// need to set up ScrollSpy on new content
		initialise_Scrollspy();

	});

}