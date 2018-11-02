$(document).ready(function(){
	$('.cancel_form').click(function(){
		document.frmSearch.reset();
		searchContributions(document.frmSearch);
	});
	
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
		searchContributions(document.frmSearch);
		return false;
		
	};
	
	searchContributions(document.frmSearch);
});

function searchContributions(frm){
	var data = fcom.frmData(frm);
	//showHtmlElementLoading($('#contributions-type-list'));
	fcom.ajax(fcom.makeUrl('Blogcontributions', 'listContributions'), data, function(t){
		$('#contributionsList').html(t);
	});
}

function paginate(p){
	var frm = document.paginateForm;
	frm.page.value = p;
	searchContributions(frm);
}
 
function confirmDelete(obj) {
	confirmbox("Are you sure you want to delete", function (outcome) {
        if(outcome){
			var data = "is_ajax_request=yes&";
		
			fcom.updateWithAjax( $(obj).attr('data-href'), data, function(t) {
				location.href = fcom.makeUrl('blogcontributions'); 
			});
		}
		 
    });
    return false;
}