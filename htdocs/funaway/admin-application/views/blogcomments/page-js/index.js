$(document).ready(function(){
	$('.cancel_form').click(function(){
		document.frmSearch.reset();
		searchComments(document.frmSearch);
	});
	loadAnchor = function(obj) {
		var href = jQuery(obj).attr('href');
		var data = '&is_ajax_request=yes';
		fcom.ajax(href, data, function (t) {
			 
			$.facebox(t);
			return false;
		});
		return false;
	}
	listPage = function( page ){
		if( typeof page == typeof undefined ){
			alert('Invalid Request!');
			return false;
		}else{
			page = parseInt( page );
			if(page < 1){
				alert('Invalid Request!');
				return false;
			}
		}
		
		$('#frmSearch').find('input[name="page"]').val(page);
		searchComments(document.frmSearch);
		return false;
		
	};
	searchComments(document.frmSearch);
});

function searchComments(frm){
	var data = fcom.frmData(frm);
	//showHtmlElementLoading($('#post-type-list'));
	fcom.ajax(fcom.makeUrl('Blogcomments', 'listComments'), data, function(t){
		$('#post-type-list').html(t);
	});
}

function paginate(p){
	var frm = document.paginateForm;
	frm.page.value = p;
	searchComments(frm);
} 

function confirmDelete(obj) {
	confirmbox("Are you sure you want to delete", function (outcome) {
        if(outcome){
			var data = "is_ajax_request=yes&";
			
			fcom.updateWithAjax( $(obj).attr('data-href'), data, function(t) {
				$('#'+$(obj).data('id')).remove();
				/* location.href = fcom.makeUrl('blogcomments');  */
			}); 
		}
    });
    return false;
}