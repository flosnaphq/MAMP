$("document").ready(function(){
	searchPaymentMethods( document.frmPaymentMethodSearchPaging, 1);
});

(function() {
	var currentPage = 1;
	searchPaymentMethods = function(frm, page){
		if (!page) {
			page = currentPage;
		}
		currentPage = page;
		var dv = $('#payment-methods-list');
		var data = fcom.frmData(frm);
		dv.html('Loading...');
		$.mbsmessage('Please wait...');
		fcom.ajax(fcom.makeUrl('PaymentMethods', 'listing', [page]), data, function(t) {
			$.mbsmessage.close();
			dv.html(t);
		});
	}
	
	changePaymentMethodStatus = function(pmethod_id,ref){
		$.mbsmessage('Please wait...');
		fcom.updateWithAjax(fcom.makeUrl('PaymentMethods', 'updateStatus', [pmethod_id]), '', function(t) {
			$.mbsmessage.close();
			if(t.status == 1){
				$(ref).toggleClass("active");
				$.systemMessage(t.msg);
			}else{
				$.systemMessage(t.msg);
			}
		});
	}
})();
/* (function() {
	
	// alert('Please note that: Unit testing is under process.');
	
	var currentPage = 1;
	
	$(document).on('click', '#admin-roles-list .statustab', function(){
		changeRoleStatus($(this));
	});
	
	setTimeout(function(){
		 searchRoles(document.frmRolesSearchPaging, 1);
	}, 500);
	
	changeRoleStatus = function(ths) {
		// alert(ths.data('id') + ' Status' + ths.data('status'));
		$.mbsmessage.fillMbsmessage('Processing...');
		fcom.updateWithAjax(fcom.makeUrl('roles', 'updateStatus', [ths.data('id')]), '', function(t) {
			if(t.status == 1){
				ths.toggleClass("active");
				$.mbsmessage.fillMbsmessage(t.msg);
				return;
			}else{
				$.mbsmessage.fillMbsmessage(t.msg);
				return;
			}
		});
	};
	
	searchRoles = function(frm, page) {
		if (!page) {
			page = currentPage;
		}
		currentPage = page;
		var dv = $('#roles-list');
		var data = fcom.frmData(frm);
		dv.html('Loading...');
		 
		fcom.ajax(fcom.makeUrl('roles', 'listing', [page]), data, function(t) {
			dv.html(t);
		});
	};
	
	listPage = function(page) {
		searchRoles(document.frmRolesSearchPaging, page);
	};
	
	reloadRolesList = function() {
		searchRoles(document.frmRolesSearchPaging, currentPage);
	}
})(); */