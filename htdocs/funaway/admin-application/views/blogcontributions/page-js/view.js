(function() {
	updateStatus = function(frm, v) {
	 
		v.validate(); 
		if (!v.isValid()){
			$('ul.errorlist').each(function(){
				$(this).parents('.field_control:first').addClass('error');
			});
			return; 
		}
		fcom.updateWithAjax($(frm).attr('action'), fcom.frmData(frm), function(t) {
			location.href = fcom.makeUrl('blogcontributions'); 
		});   
		 
		return false; 
	}
	
})();
 
function downloadFile(filename)
{
    window.location.href = '?filename=' + filename + '&msg=download';
}
 