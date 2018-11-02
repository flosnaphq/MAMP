function getComments ( postId, page ) {
	 if ( typeof postId == typeof undefined || $("#comments_area").length <= 0 ) return false;
	postId = parseInt( postId );
	if ( postId <= 0 ) return false;
	if( typeof page == typeof undefined ) page = 1;
	
	//$.mbsmessage( 'Please wait...' );
	fcom.ajax( fcom.makeUrl("Blog", "listPostComments"), 'page=' + page + '&post_id=' + postId, function(t) {
		
		if(t != ''){
			$("#comments_area").html(t);
			if( window.stButtons ) {
				stButtons.locateElements();
			}
		}
		else{
			$('#comments_area_wrapper').hide();
			$('#comments_area_wrapper').next().addClass("no--padding-top");
		}
	});  
	
	return false;
}

function blogPostComments ( page ) { 
	getComments( postId, page );
}

$( document ).ready(function(){ 
	getComments( postId, 0 );
	
}); 
