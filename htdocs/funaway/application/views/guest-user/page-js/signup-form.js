

signup = function(v){
	

var submit_btn = $("#frmRegister").find("input[type=submit]:focus" ).attr('name');
if (null === submit_btn || submit_btn == 'undefined') {
	return false;
}
var rawData = $("#frmRegister").serializeArray();
//console.log(typeof rawData);
//console.log(  rawData);
var data={};
rawData.map(function(i, e){data[i.name] = i.value});
data['submit_btn']=submit_btn;

	$('#frmRegister').ajaxSubmit({ 
			delegation: true,
			data: data,
			beforeSubmit:function(){
						v.validate();
						if (!v.isValid()){
							return false;
						} 
						jsonNotifyMessage('Processing....');
					},
			success: function(json){
				json = $.parseJSON(json);
				if(json.status == "1"){
					facebookSignUpSuccessTracker();
					jsonSuccessMessage(json.msg);
					location.reload();
				}else{
					jsonErrorMessage(json.msg);
				}
			}
		}); 
}
/* $(document).ajaxStart(function() {
	jsonNotifyMessage('Processing....');	
});  */
