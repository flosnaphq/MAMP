function getBlogPosts( page ) {
	if ( $("#post-list").length <= 0 ) return false;

	if( typeof page == typeof undefined ) page = 1;
	jsonNotifyMessage('Loading...');
	fcom.ajax( fcom.makeUrl( "blog", "listPosts" ), 'page=' + page, function(t) { 
		jsonRemoveMessage();
		$("#post-list").html(t);
		if( window.stButtons ) {
			stButtons.locateElements();
		}
	}); 
	
	return false;
}

$( document ).ready(function (){
	getBlogPosts( 1 );
});