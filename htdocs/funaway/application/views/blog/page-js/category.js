function getBlogPosts( page ) {
	if( typeof seoName == typeof undefined || $(".post-list").length <= 0 ) return false;
	else if( seoName == '' ) return false;
	
	if( typeof page == typeof undefined ) page = 1;
	
	jsonNotifyMessage('Loading...');
	fcom.ajax( fcom.makeUrl( "Blog", "listPosts" ), 'page=' + page + '&cat=' + seoName, function(t) { 
		jsonRemoveMessage();
		$(".post-list").html(t);
		if( window.stButtons ) {
			stButtons.locateElements();
		}
	}); 
	
	return false;
}

$( document ).ready(function (){
	getBlogPosts( 1 );
});
