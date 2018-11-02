
var socialWindow;
function statusChangeCallback(response) {

    if (response.status === 'connected') {
        FB.api('me', {fields: 'username'}, function (res) {
        });

        data = 'fb_token=' + response.authResponse.accessToken;
        data += '&is_ajax_request=yes';
        $.ajax({
            data: data,
            url: fcom.makeUrl("guest-user", "facebook-login"),
            dataType: 'JSON',
            type: 'POST',
            async: false,
            success: function (data) { // Do an AJAX call] 
                $(document).trigger('close.mbsmessage');

                if (data.status == "1")
                {

                    window.location = data.url;
                    //$("#responseModal").hide();
                }
                if (typeof data !== 'undefined' && data.length > 0) {
                    json = $.parseJSON(data);
                    if (json.error) {
                        error = false;

                        $.facebox("<div class='alertWrap'>" + json.msg + "</div>");
                    }
                }
            }
        });
    }
}

function fbLogin(msg) {
    jsonSuccessMessage(msg);
    FB.login(function (response) {
        statusChangeCallback(response);
    }, {scope: 'public_profile,email'});
}

function checkLoginState() {
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });

}


window.fbAsyncInit = function () {
    FB.init({
        appId: fb_app_id,
        xfbml: true,
        version: 'v2.5'
    });
};

(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


function socialLogin(url) {
    var height = 580;
    var width = 500;
    var left = 100;
    var top = 100;
    socialWindow = window.open(url, 'socialWindow', 'location=yes,status=yes,resizable=true,width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
    if (!socialWindow && !navigator.userAgent.match('iPad')) {
        alert('Cannot Open popup as it is disabled from your browser is disabled. Change settings and try again.');
    }
    else
        socialWindow.window.focus();
}

function socialError(error) {
    socialWindow.close();
    jsonErrorMessage(error);
}

function socialRedirect(url) {
    socialWindow.close();
    window.location = url;
}

//Google Sign In

var startApp = function () {
    gapi.load('auth2', function () {
        // Retrieve the singleton for the GoogleAuth library and set up the client.
        auth2 = gapi.auth2.init({
            client_id: google_app_id,
           // cookiepolicy: 'single_host_origin',
            // Request scopes in addition to 'profile' and 'email'
            //scope: 'additional_scope'
            immediate: true
        });
        attachSignin(document.getElementById('googleLogin'));
    });
};
function attachSignin(element) {

    auth2.attachClickHandler(element, {},
            function (googleUser) {
                var id_token = googleUser.getAuthResponse().id_token;
                $.ajax({
                    data:  {token: id_token},
                    url: fcom.makeUrl("guest-user", "google-login"),
                    dataType: 'JSON',
                    type: 'POST',
                    async: false,
                    success: function (data) { // Do an AJAX call] 
                        $(document).trigger('close.mbsmessage');
                        if (data.status == "1")
                        {
                            window.location = data.url;
                        }
                        if (typeof data !== 'undefined' && data.length > 0) {
                            json = $.parseJSON(data);
                            if (json.error) {
                                error = false;
                                $.facebox("<div class='alertWrap'>" + json.msg + "</div>");
                            }
                        }
                    }
                });
            }, function (error) {
              
                  $.facebox("<div class='alertWrap'>" + error['error'] + "</div>");
    });
}

$(document).ready(function () {
    startApp();
});
