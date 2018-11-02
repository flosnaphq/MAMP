function submitImage(frm, obj){
    
	obj.validate();
	if (!obj.isValid()){
		return false;
	}
	
	var data = new FormData(frm);
	data.append('fIsAjax', 1);
	data.append('fOutMode', 'json');
	var imgtag = $('#prof_img');
	var leftMenuimgtag = $('#leftmenuimgtag');
	
	$.mbsmessage('Please wait...');
	fcom.uploadFilesWithAjax( fcom.makeUrl('profile', 'updateProfileImage'), data, function(t) {
		$.mbsmessage.close();
		$.systemMessage(t.msg);
		if(t.status == '1') { 
			imgtag.attr( 'src', fcom.makeUrl( 'profile', 'profileImage', [ 270, 190, Math.random() ] ) );
			leftMenuimgtag.attr( 'src', fcom.makeUrl( 'profile', 'profileImage', [ 50, 50, Math.random() ] ) );
		}
		
	});
}

	