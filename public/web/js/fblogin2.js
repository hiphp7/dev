/**
 * Functions for login page
 */
window.onload = function(){
    // Login by Facebook
    $('#fbLogin').on('click', function(){
        // fix for chrome on iOS
        //if (navigator.userAgent.match('CriOS')) {
            window.open('https://www.facebook.com/dialog/oauth?client_id=' + facebook_app_id + '&redirect_uri=' + document.location.href + '&scope=public_profile,email,user_photos,user_friends,user_posts,user_likes,manage_pages,user_managed_groups,publish_actions&response_type=token', '', null);
            return false;
        //}        
//        FB.login(function (response) {
//            if (response.authResponse) {
//                showLoading();
//                var accessToken = response.authResponse.accessToken;                
//                var fields = 'fields=id,email,birthday,first_name,gender,last_name,link,locale,name,timezone,updated_time,verified';            
//                FB.api('/me?' + fields, function (response) {						
//                    var url = '/fblogin?backurl='+location.href+'&accessToken='+accessToken;
//                    $.ajax({
//                        cache: false,
//                        async: true,
//                        data: response,
//						type: 'post',
//                        url: url,
//                        success: function (json) {
//                            var result = jQuery.parseJSON(json);
//                            if (result.error !== 0) {
//                                alert(result.message);
//                            } else {
//                                location.href = result.backUrl;
//                            }
//                        }
//                    });
//                });
//            } else {
//                // User cancelled
//            }
//        }, {scope: 'email,user_photos,user_friends,user_posts,user_likes,manage_pages,user_managed_groups,publish_actions'});
//        return false;
    });
};
