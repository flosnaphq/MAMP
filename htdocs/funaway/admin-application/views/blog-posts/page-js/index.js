$(document).ready(function(){
	
	$('.cancel_form').click(function(){
		document.frmSearch.reset();
		searchPosts(document.frmSearch);
	});
	
	loadAnchor = function(obj) {
		$.mbsmessage('Please wait...');
		var href = jQuery(obj).attr('href');
		fcom.ajax(href, '', function (t) {
			$.mbsmessage.close();
			$.facebox(t);
			return false;
		});
		return false;
	};
	
	submitRoles = function(frm, v) {
	 
		v.validate(); 
		if (!v.isValid()){
			$('ul.errorlist').each(function(){
				$(this).parents('.field_control:first').addClass('error');
			});
			return; 
		}
		
		$.mbsmessage('Please wait...');
		fcom.updateWithAjax($(frm).attr('action'), fcom.frmData(frm), function(t) {
			$.mbsmessage.close();
			$.systemMessage(t.msg);
			setTimeout(function(){ location.href = fcom.makeUrl('BlogPosts'); }, 1000);
		});
		return false; 
		
	};
	
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
		searchPosts(document.frmSearch);
		return false;
		
	};

	searchPosts(document.frmSearch);
	
});

function searchPosts(frm){
	var data = fcom.frmData(frm);
	$.mbsmessage('Please wait...');
	fcom.ajax(fcom.makeUrl("BlogPosts", "listing"), data, function(t){
		$.mbsmessage.close();
		$("#posts_list").html(t);
	});
	return false;
}

function paginate(p){
	var frm = document.paginateForm;
	frm.page.value = p;
	searchPosts(frm);
}
// function confirmDelete(obj) {
	// confirmBox("Are you sure you want to delete", function () {
        // var data = "is_ajax_request=yes&";
		// return true;
		// // fcom.updateWithAjax( $(obj).attr('href'), data, function(t) {
			// // location.href = fcom.makeUrl('blog-posts'); 
		// // }); 
    // });
    // return false;
// }

setPopular = function(el, id){
	var status = 0;
		if($(el).hasClass('active')){
			status = 1;
		}
		$(el).toggleClass("active");
		fcom.ajax(fcom.makeUrl('blog-posts', 'markFeatured'), {"post_id":id, status : status}, function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					jsonSuccessMessage(json.msg);
				}else{
					$(el).toggleClass("active");
					jsonErrorMessage(json.msg);
				}
			});
}