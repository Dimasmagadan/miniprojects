
var comments_container = 'div#comments';
var content_container = 'div#content';
var curr_url = window.location.href;

jQuery.noConflict();

jQuery(document).ready(function() {

	// don't do this if looking for comments
	if (window.location.href.indexOf( '#comments' ) > -1) return;

	jQuery(comments_container).hide();
	
	jQuery(content_container).prepend('<hr style="height: 0" class="post-divider" data-url="' + window.location.href + '"/>');
	
	initialise_Scrollspy();
	initialise_history();

});


function initialise_history(){

	// Bind to StateChange Event
    History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
        
        var State = History.getState(); // Note: We are using History.getState() instead of event.state
        
        if (State.url != curr_url) {
        	window.location.reload(State.url);
        }
                
        console.log(State);
    });
}

function initialise_Scrollspy(){

	// spy on post-divider - changes the URL in browser location
    jQuery('.post-divider').on('scrollSpy:exit', changeURL ); 
    jQuery('.post-divider').on('scrollSpy:enter', changeURL );
    jQuery('.post-divider').scrollSpy();

	// spy on a links with rel="prev" - this does the autoloading;
	jQuery('a[rel="prev"]').on('scrollSpy:enter', doAutoLoad );
	jQuery('a[rel="prev"]').scrollSpy();	
}

function changeURL(){

	var el = jQuery(this);
	var this_url = el.attr('data-url');
	var offset = el.offset();
	var scrollTop = jQuery(document).scrollTop();
		
	// if exiting or entering from top, change URL 
	if ( ( offset.top - scrollTop ) < 100 ) {
		curr_url = this_url;
		History.pushState(null, null, this_url );
	} 
	
	
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
	
		var $post_html = jQuery( '<hr class="post-divider" data-url="' + post_url + '"/>' +	data ); 
		
		jQuery( content_container ).append( $post_html );
		
		// update the browser location
		//History.pushState(null, null, post_url);
		 	
		// need to set up ScrollSpy on new content
		initialise_Scrollspy();

	});

}

function getOffset( el ) {
    var _x = 0;
    var _y = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop - el.scrollTop;
        el = el.offsetParent;
    }
    return { top: _y, left: _x };
}