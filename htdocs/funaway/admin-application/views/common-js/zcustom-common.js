(function($){
	var runningAjaxRequest = false;
	var files;
	
	$.extend(fcom, {
		
		uploadFilesWithAjax: function(url, data, fn, options) {
			/* $.mbsmessage('Please wait...'); */
			var o = $.extend(true, {fOutMode:'json', timeout: 20000, maxRetry: 3, retryNumber: 0}, options);
			
			$.ajax({
				url: url,
				type: 'POST',
				data: data,
				dataType: o.fOutMode,
				processData: false, 
				contentType: false,
				success: function(t){
					
					if (o.fOutMode == 'json') {
						if (t.status != 1) {
							$.systemMessage(t.msg);
							if (options.errorFn) {
								options.errorFn();
							}
							return ;
						}
					}
					fn(t);
				},
				error: function(jqXHR, textStatus, error) {
					if(textStatus == "parsererror" && jqXHR.statusText == "OK") {
				        alert('Seems some json error.' + jqXHR.responseText);
				        return ;
				    }
					
					
					o.retryNumber++;
					if (o.retryNumber <= o.maxRetry) {
						console.log('Will retry ' + o.retryNumber);
						setTimeout(function() {
							fcom.ajax(url, data, fn, o)
						}, 3000);
					}
					else {
						if (!options.errorFn) {
							alert(jqXHR.statusText + textStatus);
						}
					}
					
					console.log( "Ajax Request " + url + " error: " + textStatus + " -- " + error);
					if (options.errorFn) {
						options.errorFn();
					}
				},
				timeout: o.timeout
			});
		}
	});

	clearFatCache = function() {
		if(true === runningAjaxRequest) {
			return;
		}
		
		runningAjaxRequest = true;
		jsonNotifyMessage('Please wait...');
		fcom.ajax(fcom.makeUrl('Home', 'clearFatCache'), {}, function (response) {
			runningAjaxRequest = false;
			jsonNotifyMessage(response);
		});
	};
	
})(jQuery);