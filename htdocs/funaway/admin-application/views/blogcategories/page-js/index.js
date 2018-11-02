$(document).ready(function () {
	$('.cancel_form').click(function(){
		window.location.href = ''; //fcom.makeUrl('Blogcategories');
	}); 
	searchBlogCatogries(document.frmSearch);	 
});

function searchBlogCatogries(frm , parentSelect = true) {
    var data = "";

    if (typeof (frm) != "undefined") {
        var data = fcom.frmData(frm);
	}
	 
	data += "&category_parent=" + catId;
	fcom.ajax(fcom.makeUrl('Blogcategories', 'listBlogCategories'), data, function(t){
		$('#listing-div').html(t);}
	);
}

function toggleStatus(fm){
	bcatid = parseInt(fm.id);
	if(bcatid < 1){
		alert('Invalid Request!');
		return false;
	}
	fcom.updateWithAjax(fcom.makeUrl('Blogcategories', 'changeStatus'), 'category_id='+bcatid, function(t) {
		jsonSuccessMessage(t.msg);
		$(fm).toggleClass("active");
		setTimeout(function(){ location.href = ''; }, 1000);
	});
	return false;
} 

function filteredBlogCatogries(frm) {
    var data = "";

    if (typeof (frm) != "undefined") {
        var data = fcom.frmData(frm);
	}
    data += "&category_parent=" + catId;
     
	fcom.ajax(fcom.makeUrl('Blogcategories', 'filteredBlogCatogries'), data, function(t){
		$('#listing-div').html(t);}
	);
}

function paginate(p) {
    var frm = document.paginateForm;
    frm.page.value = p;
    searchBlogCatogries(frm);
}
 