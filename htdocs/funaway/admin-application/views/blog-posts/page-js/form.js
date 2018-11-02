(function() {
 	
	submitPost = function(frms, v) {
		$("#post_content_en").html($("#idContentoEdit1").contents().find("html > body").html());	
		 
		
	 	v.validate();
		if (!v.isValid()){ 
			$('ul.errorlist').each(function(){
				$(this).parents('.field_control:first').addClass('error');
			});
			return false; 
		} 
		
		return true; 
		 
	}

		
})();
function setMainImage(el, id, blog_post_id){
	 
	id = parseInt(id);
	//showHtmlElementLoading($('#post_imgs'));
	 
	fcom.updateWithAjax(fcom.makeUrl('BlogPosts', 'setMainImage'), 'imgid='+id+'&blog_post_id='+blog_post_id, function(t) {
		$('#post_imgs').html(t.imagesHtml);
		jsonSuccessMessage(t.msg); 
	});
	return false;
}

function removeImage(el, id){
	id = parseInt(id);
	$(el).parent('.photosquare').remove();
	var rimg = $('#post_removed_images');
	var rimg_content = rimg.val();
	if(rimg_content != ''){
		var rimg_arr = rimg_content.split(',');
		rimg_arr.push(id);
		rimg_content = rimg_arr.join(',');
	}else{
		rimg_content = id;
	}
	rimg.val(rimg_content);
	return false;
}

function cancelPost() {
	window.location.href = fcom.makeUrl('blogposts');
}


$(document).ready(function(){
	// window["oEdit1"] = new InnovaEditor("oEdit1");
	// window["oEdit1"].width = "100%";
	// window["oEdit1"].REPLACE("post_content_en", "editor-box");  
	
	// window["oEdit1"] .fileBrowser = fcom.makeUrl('')+"innova/assetmanager/asset.php";				
	
	
	$("#add_more_field").click(function (e) {
		$("#image_div").append('<div><input type="file" name="post_image_file_name[]" accept="image/*"><button  class="delete">Delete</button></div>');
	});

	$("body").on("click", ".delete", function (e) {
		$(this).parent("div").remove();
	});
});

function setSeoName(el, fld_id){
	txt_val = el.value;
	if(txt_val.trim()){
		txt_val=$.trim(txt_val.toLowerCase());
		/*txt_val=txt_val.replace(/[^a-zA-Z0-9 ]+/g,"-");*/
		txt_val=txt_val.replace(/[^a-zA-Z0-9 ]+/g,"-");
		txt_val=txt_val.replace(/\s+/g, "-");
		txt_val=$.trim(txt_val);
		txt_val=rtrim(txt_val, '-');
		$('#'+fld_id.id).val(txt_val);
	}
	return;
}

function rtrim(str, chr) {
  var rgxtrim = (!chr) ? new RegExp('\\s+$') : new RegExp(chr+'+$');
  return str.replace(rgxtrim, '');
}
 