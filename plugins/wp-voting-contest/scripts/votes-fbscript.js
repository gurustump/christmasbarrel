function statusChangeCallback(response,flag) {    
    
    if(flag == 1){
        
     FB.api('/me', function(response) {
      jQuery.ajax({
		  type: 'POST',
		  url: zn_do_login.ajaxurl,		 
		  data: {
		  action : 'zn_fb_login',
		  responses: response,	
          email: response.email,		 
		  },
		  success: function(response, textStatus, XMLHttpRequest){	
			 
             location.reload();			
		  },
		  error: function(MLHttpRequest, textStatus, errorThrown){
			alert(errorThrown);
		  }
	   });    
    }); 
      
    }    
    if (response.status === 'connected') {      
      testAPI();
    } 
}

function checkLoginState() {
    FB.getLoginStatus(function(response) {    
        statusChangeCallback(response,1);      
    });
}

var vote_fb_appid = jQuery('#vote_fb_appid').val();  
if(vote_fb_appid != null)
{
      window.fbAsyncInit = function() {
      FB.init({
        appId      : vote_fb_appid,
        status : true,
        cookie     : true,  // enable cookies to allow the server to access 
                            // the session
        xfbml      : true,  // parse social plugins on this page
        version    : 'v2.0', // use version 2.0
        oauth : true,
      });
      FB.getLoginStatus(function(response) {
        statusChangeCallback(response,0);
      });    
    };
    
    // Load the SDK asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  
    //whenever the user logs in, we refresh the page
} 
function testAPI() {   
    FB.api('/me', function(response) {           
                
    });      
}

function voting_save_twemail_session()
{
    jQuery.prettyPhoto.close();  
    jQuery(".error_empty").remove();  
    setTimeout(function() {
        jQuery.fn.prettyPhoto({social_tools: false, deeplinking: false, show_title: false, default_width: '350', theme:'pp_kalypso'});
    	jQuery.prettyPhoto.open('#twitter_register_panel');
     }, 300);
    jQuery( ".inner-container" ).addClass('forgot-panel_add');
}

function votes_twitter_authentication()
{       
    jQuery.ajax({
	  type: 'POST',
	  url: zn_do_login.ajaxurl,		 
	  data: {
	  action : 'zn_tw_login',
	  vote_tw_appid: jQuery('#vote_tw_appid').val(),	
      vote_tw_secret: jQuery('#vote_tw_secret').val(), 
      current_callback_url : jQuery('#current_callback_url').val(),    	 
	  },
	  success: function(response, textStatus, XMLHttpRequest){
	      window.location.href = response;         			
	  },
	  error: function(MLHttpRequest, textStatus, errorThrown){
		alert(errorThrown);
	  }
   });
   
}


var getCookies = function(){
  var pairs = document.cookie.split(";");
  var cookies = {};
  for (var i=0; i<pairs.length; i++){
    var pair = pairs[i].split("=");
    cookies[pair[0]] = unescape(pair[1]);
  }
  return cookies;
}
var myCookies = getCookies();